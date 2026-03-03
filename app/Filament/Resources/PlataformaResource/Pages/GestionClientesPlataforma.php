<?php

namespace App\Filament\Resources\PlataformaResource\Pages;

use App\Filament\Resources\PlataformaResource;
use App\Models\Cuenta;
use App\Models\Perfil;
use App\Support\ClientMessageBuilder;
use App\Support\UserPreferenceState;
use Closure;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;

class GestionClientesPlataforma extends ManageRelatedRecords
{
    protected array $accountColorMap = [];
    protected array $clientProfilesCache = [];
    protected array $clientDistributedCache = [];
    protected bool $clientProfilesCacheLoaded = false;
    protected ?bool $shouldColorizeAccounts = null;

    protected static string $resource = PlataformaResource::class;

    protected static string $relationship = 'perfiles';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static function hasPermission(string $permission): bool
    {
        $user = auth()->user();

        return $user?->hasRole('administrador') || $user?->can($permission);
    }

    public static function canAccess(array $parameters = []): bool
    {
        return static::hasPermission('clientes.view');
    }

    public function getTitle(): string
    {
        return 'Clientes de ' . $this->getRecord()->nombre;
    }

    public function form(Form $form): Form
    {
        return $form->schema($this->clienteFormSchema());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('cliente_nombre')
            ->recordAction('ver')
            ->modifyQueryUsing(fn ($query) => $query
                ->orderByRaw('LOWER(TRIM(correo_cuenta)) asc')
                ->orderByRaw('CAST(nombre_perfil AS UNSIGNED) asc')
                ->orderBy('id', 'asc'))
            ->recordClasses(fn (Perfil $record): ?string => $this->isClientDistributedAcrossAccounts($record)
                ? 'bg-warning-50/40 dark:bg-warning-900/20'
                : null)
            ->columns([
                Tables\Columns\TextColumn::make('cliente_posicion')
                    ->label('#')
                    ->alignment('center')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('proveedor_nombre')->label('Proveedor')->searchable(),
                Tables\Columns\TextColumn::make('cuenta.fecha_inicio')
                    ->label('Fecha de inicio')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date(),
                Tables\Columns\TextColumn::make('fecha_caducidad_cuenta')->label('Fecha de caducidad')->date(),
                Tables\Columns\TextColumn::make('correo_cuenta')
                    ->label('Correo')
                    ->searchable()
                    ->formatStateUsing(fn (?string $state): string => $this->renderAccountBadge($state))
                    ->html(),
                Tables\Columns\TextColumn::make('contrasena_cuenta')
                    ->label('Contraseña')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('cliente_nombre')
                    ->label('Nombre del cliente')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cliente_telefono')
                    ->label('Número de teléfono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre_perfil')
                    ->label('Número de perfil')
                    ->searchable()
                    ->formatStateUsing(function ($state): string {
                        $value = (string) $state;

                        if (preg_match('/\d+/', $value, $matches)) {
                            return (string) ((int) $matches[0]);
                        }

                        return $value;
                    }),
                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Fecha inicio')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date(),
                Tables\Columns\TextColumn::make('fecha_corte')
                    ->label('Fecha corte')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date(),
                Tables\Columns\TextColumn::make('dias_restantes')
                    ->label('Cuenta regresiva')
                    ->alignment('center')
                    ->badge()
                    ->color(fn ($state) => $state === null ? 'gray' : ($state <= 0 ? 'danger' : ($state <= 5 ? 'warning' : 'success')))
                    ->formatStateUsing(fn ($state) => $state === null ? '-' : (string) $state),
            ])
            ->headerActions([
                Tables\Actions\Action::make('verResumenCuentas')
                    ->label('Ver resumen de cuentas')
                    ->icon('heroicon-o-chart-bar-square')
                    ->visible(fn () => static::hasPermission('clientes.view'))
                    ->modalHeading('Resumen por cuentas')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalContent(fn () => view('filament.plataformas.resumen-cuentas', [
                        'cuentas' => $this->getAccountsSummary(),
                        'limite' => (int) ($this->getRecord()->perfiles_por_cuenta ?: 5),
                    ])),
                Tables\Actions\CreateAction::make('agregarCliente')
                    ->label('Agregar cliente')
                    ->successNotificationTitle('Registro creado correctamente.')
                    ->visible(fn () => static::hasPermission('clientes.create'))
                    ->form($this->clienteCreateFormSchema())
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['plataforma_id'] = $this->getRecord()->id;

                        return $data;
                    })
                    ->using(function (array $data): Perfil {
                        $cantidad = max((int) ($data['cantidad_perfiles'] ?? 1), 1);
                        $cuentaId = (int) ($data['cuenta_id'] ?? 0);
                        unset($data['cantidad_perfiles']);

                        try {
                            return DB::transaction(function () use ($data, $cantidad) {
                                $assignments = $this->resolveAssignmentsAcrossAccounts((int) ($data['cuenta_id'] ?? 0), $cantidad, null, true);
                                $perfilCreado = null;

                                foreach ($assignments as $assignment) {
                                    $payload = $this->hydratePayloadWithCuentaData($data, $assignment['cuenta']);
                                    $payload['nombre_perfil'] = (string) $assignment['slot'];

                                    $perfil = Perfil::create($payload);

                                    if (!$perfilCreado) {
                                        $perfilCreado = $perfil;
                                    }
                                }

                                $result = $perfilCreado;

                                $this->resetTransientCaches();

                                return $result;
                            });
                        } catch (QueryException $exception) {
                            if ($this->isDuplicateProfileConstraint($exception)) {
                                $disponibles = $this->getTotalAvailableSlotsForAllocation($cuentaId);

                                throw ValidationException::withMessages([
                                    'cantidad_perfiles' => "Solamente {$disponibles} perfiles disponibles entre todas las cuentas",
                                ]);
                            }

                            throw $exception;
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('mensaje')
                    ->label('Mensaje')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->modalHeading('Mensaje para cliente')
                    ->modalSubmitAction(false)
                    ->modalContent(fn (Perfil $record) => view('filament.modals.mensaje-credenciales', [
                        'mensaje' => $this->buildClientMessage($record),
                    ]))
                    ->action(fn () => null),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('ver')
                        ->label('Ver')
                        ->icon('heroicon-o-eye')
                        ->modalHeading('Detalle del cliente')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Cerrar')
                        ->modalContent(fn (Perfil $record) => view('filament.modals.detalle-cliente', [
                            'perfil' => $record->loadMissing(['plataforma', 'cuenta']),
                        ])),
                    Tables\Actions\EditAction::make()
                        ->successNotificationTitle('Cambios guardados correctamente.')
                        ->visible(fn () => static::hasPermission('clientes.edit'))
                        ->form(fn (Perfil $record): array => $this->clienteEditFormSchema($record))
                        ->using(fn (Perfil $record, array $data): Perfil => $this->updateClientBundleFromEdit($record, $data)),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn () => static::hasPermission('clientes.delete'))
                        ->requiresConfirmation()
                        ->modalHeading('Confirmar eliminación')
                        ->modalDescription(function (Perfil $record): string {
                            $total = $this->getClientBundleRecords($record)->count();

                            return "Se eliminarán {$total} perfiles de este cliente. Esta acción no se puede deshacer.";
                        })
                        ->modalSubmitActionLabel('Eliminar')
                        ->successNotificationTitle('Registro eliminado correctamente.')
                        ->action(function (Perfil $record): void {
                            DB::transaction(function () use ($record): void {
                                $this->getClientBundleRecords($record)->each(fn (Perfil $perfil) => $perfil->delete());
                            });

                            $this->resetTransientCaches();
                        }),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->label(''),
            ])
                    ->actionsColumnLabel('Acción')
                    ->actionsAlignment('center')
            ->bulkActions([]);
    }

    protected function clienteFormSchema(?Perfil $contextRecord = null): array
    {
        return [
            Forms\Components\TextInput::make('cliente_nombre')->label('Nombre cliente')->required()->maxLength(120),
            Forms\Components\TextInput::make('cliente_telefono')->label('Número teléfono')->required()->maxLength(30),
            Forms\Components\Select::make('cuenta_id')
                ->label('Cuenta')
                ->options(fn (): array => $this->getAvailableCuentaOptions($contextRecord))
                ->searchable()
                ->preload()
                ->default(fn (): ?int => $this->resolveCuentaIdForPerfil($contextRecord))
                ->afterStateHydrated(function (Forms\Components\Select $component, $state) use ($contextRecord): void {
                    if (filled($state) || ! $contextRecord) {
                        return;
                    }

                    $resolvedCuentaId = $this->resolveCuentaIdForPerfil($contextRecord);

                    if (filled($resolvedCuentaId)) {
                        $component->state((int) $resolvedCuentaId);
                    }
                })
                ->required()
                ->live(debounce: 300)
                ->helperText('Se muestran cuentas con cupos disponibles. En edición, la cuenta actual permanece visible aunque no tenga cupos.'),
            Forms\Components\TextInput::make('pin')->label('PIN')->maxLength(20),
            Forms\Components\DatePicker::make('fecha_caducidad_cuenta')->label('Fecha caducidad')->required(),
        ];
    }

    protected function clienteCreateFormSchema(): array
    {
        $schema = $this->clienteFormSchema(null);

        array_splice($schema, 3, 0, [
            Forms\Components\TextInput::make('cantidad_perfiles')
                ->label('Cantidad de perfiles a asignar')
                ->numeric()
                ->minValue(1)
                ->default(1)
                ->required()
                ->live(debounce: 300)
                ->rule(function (Get $get): Closure {
                    return function (string $attribute, $value, Closure $fail) use ($get): void {
                        $cuentaId = (int) ($get('cuenta_id') ?? 0);
                        $cantidad = max((int) $value, 0);

                        if ($cuentaId <= 0 || $cantidad <= 0) {
                            return;
                        }

                        $disponibles = $this->getTotalAvailableSlotsForAllocation($cuentaId);

                        if ($cantidad > $disponibles) {
                            $fail("Solamente {$disponibles} perfiles disponibles entre todas las cuentas");
                        }
                    };
                })
                ->helperText(function (Get $get): string {
                    $cuentaId = (int) ($get('cuenta_id') ?? 0);

                    if ($cuentaId <= 0) {
                        return 'Selecciona primero una cuenta para calcular perfiles disponibles.';
                    }

                    $disponiblesCuenta = $this->getAvailableSlotsForCuentaId($cuentaId);
                    $disponiblesTotales = $this->getTotalAvailableSlotsForAllocation($cuentaId);

                    return "Disponibles en la cuenta seleccionada: {$disponiblesCuenta}. Disponibles totales (incluyendo otras cuentas): {$disponiblesTotales}";
                }),
        ]);

        return $schema;
    }

    protected function clienteEditFormSchema(Perfil $record): array
    {
        $schema = $this->clienteFormSchema($record);

        array_splice($schema, 3, 0, [
            Forms\Components\TextInput::make('cantidad_perfiles')
                ->label('Cantidad de perfiles a asignar')
                ->numeric()
                ->minValue(1)
                ->default($this->getClientBundleRecords($record)->count())
                ->afterStateHydrated(function (Forms\Components\TextInput $component, $state) use ($record): void {
                    if (filled($state)) {
                        return;
                    }

                    $component->state($this->getClientBundleRecords($record)->count());
                })
                ->required()
                ->live(debounce: 300)
                ->rule(function (Get $get) use ($record): Closure {
                    return function (string $attribute, $value, Closure $fail) use ($get, $record): void {
                        $cuentaId = (int) ($get('cuenta_id') ?? 0);
                        $cantidad = max((int) $value, 0);

                        if ($cuentaId <= 0 || $cantidad <= 0) {
                            return;
                        }

                        $disponibles = $this->getTotalAvailableSlotsForAllocationOnEdit($cuentaId, $record);

                        if ($cantidad > $disponibles) {
                            $fail("Solamente {$disponibles} perfiles disponibles entre todas las cuentas");
                        }
                    };
                })
                ->helperText(function (Get $get) use ($record): string {
                    $cuentaId = (int) ($get('cuenta_id') ?? 0);

                    if ($cuentaId <= 0) {
                        return 'Selecciona primero una cuenta para calcular perfiles disponibles.';
                    }

                    $disponiblesCuenta = $this->getAvailableSlotsForCuentaIdOnEdit($cuentaId, $record);
                    $disponiblesTotales = $this->getTotalAvailableSlotsForAllocationOnEdit($cuentaId, $record);

                    return "Disponibles en la cuenta seleccionada: {$disponiblesCuenta}. Disponibles totales (incluyendo otras cuentas): {$disponiblesTotales}";
                }),
        ]);

        return $schema;
    }

    protected function buildClientMessage(Perfil $perfil): string
    {
        return ClientMessageBuilder::buildDeliveryMessage($perfil);
    }

    protected function resolveNextProfileSlot(string $correoCuenta): string
    {
        return (string) ($this->resolveNextProfileSlots($correoCuenta, 1)[0] ?? 1);
    }

    protected function resolveNextProfileSlots(string $correoCuenta, int $cantidad, bool $forUpdate = false): array
    {
        $correoCuenta = mb_strtolower(trim($correoCuenta));
        $limite = (int) ($this->getRecord()->perfiles_por_cuenta ?: 5);

        $occupancy = $this->getAccountOccupancyData($correoCuenta, null, $forUpdate);
        $disponibles = max($limite - $occupancy['total'], 0);

        if ($cantidad > $disponibles) {
            throw ValidationException::withMessages([
                'cantidad_perfiles' => "{$disponibles} perfiles disponibles para esta cuenta",
            ]);
        }

        $slots = [];

        for ($slot = 1; $slot <= $limite; $slot++) {
            if (!$occupancy['numericSlots']->contains($slot)) {
                $slots[] = $slot;

                if (count($slots) === $cantidad) {
                    return $slots;
                }
            }
        }

        throw ValidationException::withMessages([
            'cantidad_perfiles' => "{$disponibles} perfiles disponibles para esta cuenta",
        ]);
    }

    protected function resolveNextProfileSlotsForEdit(string $correoCuenta, int $cantidad, Perfil $record, bool $forUpdate = false): array
    {
        $correoCuenta = $this->normalizeAccountEmail($correoCuenta);
        $limite = (int) ($this->getRecord()->perfiles_por_cuenta ?: 5);
        $bundleRecords = $this->getClientBundleRecords($record);
        $bundleIds = $bundleRecords->pluck('id');

        $occupancy = $this->getAccountOccupancyData($correoCuenta, $bundleIds, $forUpdate);
        $disponibles = max($limite - $occupancy['total'], 0);

        if ($cantidad > $disponibles) {
            throw ValidationException::withMessages([
                'cantidad_perfiles' => "Solamente {$disponibles} perfiles disponibles para esta cuenta",
            ]);
        }

        $slots = [];

        for ($slot = 1; $slot <= $limite; $slot++) {
            if (!$occupancy['numericSlots']->contains($slot)) {
                $slots[] = $slot;

                if (count($slots) === $cantidad) {
                    return $slots;
                }
            }
        }

        throw ValidationException::withMessages([
            'cantidad_perfiles' => "Solamente {$disponibles} perfiles disponibles para esta cuenta",
        ]);
    }

    protected function getAvailableSlotsForAccount(string $correoCuenta): int
    {
        $correoCuenta = $this->normalizeAccountEmail($correoCuenta);
        $limite = (int) ($this->getRecord()->perfiles_por_cuenta ?: 5);

        $occupancy = $this->getAccountOccupancyData($correoCuenta);

        return max($limite - $occupancy['total'], 0);
    }

    protected function getAvailableSlotsForAccountOnEdit(string $correoCuenta, Perfil $record): int
    {
        $correoCuenta = $this->normalizeAccountEmail($correoCuenta);
        $limite = (int) ($this->getRecord()->perfiles_por_cuenta ?: 5);
        $bundleIds = $this->getClientBundleRecords($record)->pluck('id');

        $occupancy = $this->getAccountOccupancyData($correoCuenta, $bundleIds);

        return max($limite - $occupancy['total'], 0);
    }

    protected function getAccountOccupancyData(string $correoCuenta, ?Collection $excludedIds = null, bool $forUpdate = false): array
    {
        $query = Perfil::query()
            ->where('plataforma_id', $this->getRecord()->id)
            ->whereRaw('LOWER(TRIM(correo_cuenta)) = ?', [$correoCuenta]);

        if ($excludedIds && $excludedIds->isNotEmpty()) {
            $query->whereNotIn('id', $excludedIds);
        }

        if ($forUpdate) {
            $query->lockForUpdate();
        }

        $nombres = $query->pluck('nombre_perfil');

        return [
            'total' => $nombres->count(),
            'numericSlots' => $nombres
                ->map(fn ($perfil) => (int) $perfil)
                ->filter(fn (int $perfil) => $perfil > 0)
                ->unique()
                ->values(),
        ];
    }

    protected function getClientBundleRecords(Perfil $record): EloquentCollection
    {
        if (filled($record->cuenta_id)) {
            return Perfil::query()
                ->where('plataforma_id', $record->plataforma_id)
                ->where('cliente_nombre', $record->cliente_nombre)
                ->where('cuenta_id', $record->cuenta_id)
                ->orderBy('id')
                ->get();
        }

        $correoCuenta = $this->normalizeAccountEmail((string) $record->correo_cuenta);

        return Perfil::query()
            ->where('plataforma_id', $record->plataforma_id)
            ->where('cliente_nombre', $record->cliente_nombre)
            ->whereRaw('LOWER(TRIM(correo_cuenta)) = ?', [$correoCuenta])
            ->orderBy('id')
            ->get();
    }

    protected function updateClientBundleFromEdit(Perfil $record, array $data): Perfil
    {
        $cuentaId = (int) ($data['cuenta_id'] ?? 0);
        $cantidad = max((int) ($data['cantidad_perfiles'] ?? 1), 1);
        $bundleRecords = $this->getClientBundleRecords($record)->values();

        unset($data['cantidad_perfiles']);

        try {
            return DB::transaction(function () use ($bundleRecords, $cantidad, $data, $record): Perfil {
                $assignments = $this->resolveAssignmentsAcrossAccounts(
                    (int) ($data['cuenta_id'] ?? 0),
                    $cantidad,
                    $bundleRecords->pluck('id'),
                    true
                );
            $result = null;

            foreach ($assignments as $index => $assignment) {
                $payload = $this->hydratePayloadWithCuentaData($data, $assignment['cuenta']);
                $payload['nombre_perfil'] = (string) $assignment['slot'];

                $perfil = $bundleRecords->get($index);

                if ($perfil) {
                    $perfil->update($payload);
                    $result ??= $perfil->fresh();

                    continue;
                }

                $created = Perfil::create($payload);
                $result ??= $created;
            }

                if ($bundleRecords->count() > count($assignments)) {
                $bundleRecords
                    ->slice(count($assignments))
                    ->each(fn (Perfil $perfil) => $perfil->delete());
            }

            $this->resetTransientCaches();

            return $result ?? $bundleRecords->first();
            });
        } catch (QueryException $exception) {
            if ($this->isDuplicateProfileConstraint($exception)) {
                $disponibles = $this->getTotalAvailableSlotsForAllocationOnEdit($cuentaId, $record);

                throw ValidationException::withMessages([
                    'cantidad_perfiles' => "Solamente {$disponibles} perfiles disponibles entre todas las cuentas",
                ]);
            }

            throw $exception;
        }
    }

    protected function resolveAccountColor(?string $correoCuenta, string $variant = 'solid'): string
    {
        $correoCuenta = $this->normalizeAccountEmail($correoCuenta ?? '');

        if ($correoCuenta === '') {
            return $variant === 'soft' ? '#f3f4f6' : '#6b7280';
        }

        if (!isset($this->accountColorMap[$correoCuenta])) {
            $this->accountColorMap[$correoCuenta] = $this->generateAccountColorData($correoCuenta);
        }

        $colorData = $this->accountColorMap[$correoCuenta] ?? [
            'solid' => '#2563eb',
            'soft' => '#dbeafe',
            'ring' => '#93c5fd',
        ];

        return $colorData[$variant] ?? $colorData['solid'];
    }

    protected function normalizeAccountEmail(string $correoCuenta): string
    {
        return mb_strtolower(trim($correoCuenta));
    }

    protected function getAccountColorMap(): array
    {
        return $this->accountColorMap;
    }

    protected function generateAccountColorData(string $correoCuenta): array
    {
        $hash = md5($correoCuenta);
        $hue = (int) floor((hexdec(substr($hash, 0, 2)) / 255) * 360);
        $saturation = 72 + (hexdec(substr($hash, 2, 2)) % 20);
        $solidLightness = 34 + (hexdec(substr($hash, 4, 2)) % 16);
        $softLightness = 90 + (hexdec(substr($hash, 6, 2)) % 8);
        $ringLightness = 62 + (hexdec(substr($hash, 8, 2)) % 18);

        return [
            'solid' => "hsl({$hue} {$saturation}% {$solidLightness}%)",
            'soft' => "hsl({$hue} {$saturation}% {$softLightness}%)",
            'ring' => "hsl({$hue} {$saturation}% {$ringLightness}%)",
        ];
    }

    protected function renderAccountBadge(?string $correoCuenta): string
    {
        $correoCuenta = $this->normalizeAccountEmail($correoCuenta ?? '');

        if ($correoCuenta === '') {
            return "<span class='text-gray-500'>-</span>";
        }

        $correoEscapado = e($correoCuenta);

        if (! $this->shouldColorizeAccounts()) {
            return "<span class='font-medium text-gray-900 dark:text-gray-100'>{$correoEscapado}</span>";
        }

        $solid = $this->resolveAccountColor($correoCuenta, 'solid');

        return "<span style=\"color:{$solid};font-weight:600;\">{$correoEscapado}</span>";
    }

    protected function shouldColorizeAccounts(): bool
    {
        if ($this->shouldColorizeAccounts !== null) {
            return $this->shouldColorizeAccounts;
        }

        $preferences = UserPreferenceState::forUser(auth()->user());

        $this->shouldColorizeAccounts = (bool) ($preferences['colorize_accounts'] ?? true);

        return $this->shouldColorizeAccounts;
    }

    protected function getAccountsSummary(): Collection
    {
        $limite = (int) ($this->getRecord()->perfiles_por_cuenta ?: 5);

        return Perfil::query()
            ->selectRaw('LOWER(TRIM(correo_cuenta)) as correo_normalizado')
            ->selectRaw('COUNT(*) as total_registros')
            ->selectRaw('COUNT(*) as ocupados')
            ->where('plataforma_id', $this->getRecord()->id)
            ->whereNotNull('correo_cuenta')
            ->where('correo_cuenta', '!=', '')
            ->groupBy('correo_normalizado')
            ->orderBy('correo_normalizado')
            ->get()
            ->map(function ($row) use ($limite) {
                $correo = (string) $row->correo_normalizado;
                $ocupados = min((int) $row->ocupados, $limite);
                $libres = max($limite - $ocupados, 0);
                $solid = $this->resolveAccountColor($correo, 'solid');
                $soft = $this->resolveAccountColor($correo, 'soft');
                $ring = $this->resolveAccountColor($correo, 'ring');

                return [
                    'correo' => $correo,
                    'ocupados' => $ocupados,
                    'libres' => $libres,
                    'limite' => $limite,
                    'solidColor' => $solid,
                    'softColor' => $soft,
                    'ringColor' => $ring,
                ];
            });
    }

    protected function getClientProfilesData(Perfil $record): array
    {
        $this->ensureClientProfilesCacheLoaded();

        $key = $this->resolveClientBundleCacheKey($record);

        if (isset($this->clientProfilesCache[$key])) {
            return $this->clientProfilesCache[$key];
        }

        return ['total' => 1, 'lista' => (string) $record->nombre_perfil];
    }

    protected function resetTransientCaches(): void
    {
        $this->accountColorMap = [];
        $this->clientProfilesCache = [];
        $this->clientDistributedCache = [];
        $this->clientProfilesCacheLoaded = false;
        $this->shouldColorizeAccounts = null;
    }

    protected function ensureClientProfilesCacheLoaded(): void
    {
        if ($this->clientProfilesCacheLoaded) {
            return;
        }

        $rows = Perfil::query()
            ->where('plataforma_id', $this->getRecord()->id)
            ->get(['cuenta_id', 'cliente_nombre', 'correo_cuenta', 'nombre_perfil']);

        $grouped = $rows->groupBy(function (Perfil $perfil): string {
            return $this->resolveClientBundleCacheKey($perfil);
        });

        $this->clientProfilesCache = $grouped->map(function (Collection $items): array {
            $perfiles = $items
                ->pluck('nombre_perfil')
                ->map(fn ($perfil) => (string) $perfil)
                ->unique()
                ->sort(function (string $a, string $b): int {
                    $na = is_numeric($a) ? (int) $a : PHP_INT_MAX;
                    $nb = is_numeric($b) ? (int) $b : PHP_INT_MAX;

                    return $na === $nb ? strcmp($a, $b) : ($na <=> $nb);
                })
                ->values();

            return [
                'total' => $perfiles->count(),
                'lista' => $perfiles->isEmpty() ? '-' : $perfiles->implode(', '),
            ];
        })->all();

        $this->clientDistributedCache = $grouped->map(function (Collection $items): bool {
            return $items
                ->pluck('cuenta_id')
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->count() > 1;
        })->all();

        $this->clientProfilesCacheLoaded = true;
    }

    protected function isClientDistributedAcrossAccounts(Perfil $record): bool
    {
        $this->ensureClientProfilesCacheLoaded();
        $key = $this->resolveClientBundleCacheKey($record);

        return (bool) ($this->clientDistributedCache[$key] ?? false);
    }

    protected function resolveClientBundleCacheKey(Perfil $record): string
    {
        if (filled($record->cuenta_id)) {
            return implode('|', [$this->getRecord()->id, (int) $record->cuenta_id, mb_strtolower(trim((string) $record->cliente_nombre))]);
        }

        $correo = $this->normalizeAccountEmail((string) $record->correo_cuenta);
        $nombreCliente = trim((string) $record->cliente_nombre);

        return implode('|', [$this->getRecord()->id, $correo, mb_strtolower($nombreCliente)]);
    }

    protected function isDuplicateProfileConstraint(QueryException $exception): bool
    {
        return str_contains($exception->getMessage(), 'perfiles_plataforma_correo_nombre_unique');
    }

    protected function getAvailableSlotsForCuentaId(int $cuentaId): int
    {
        $cuenta = $this->getCuentaFromId($cuentaId);

        if (! $cuenta) {
            return 0;
        }

        return $this->getAvailableSlotsForCuenta($cuenta);
    }

    protected function getAvailableSlotsForCuentaIdOnEdit(int $cuentaId, Perfil $record): int
    {
        $cuenta = $this->getCuentaFromId($cuentaId);

        if (! $cuenta) {
            return 0;
        }

        return $this->getAvailableSlotsForCuenta($cuenta, $this->getClientBundleRecords($record)->pluck('id'));
    }

    protected function getTotalAvailableSlotsForAllocation(int $cuentaId): int
    {
        return $this->getTotalAvailableSlotsAcrossAccounts($cuentaId);
    }

    protected function getTotalAvailableSlotsForAllocationOnEdit(int $cuentaId, Perfil $record): int
    {
        return $this->getTotalAvailableSlotsAcrossAccounts($cuentaId, $this->getClientBundleRecords($record)->pluck('id'));
    }

    protected function getCorreoCuentaFromCuentaId(int $cuentaId): string
    {
        $cuenta = $this->getCuentaFromId($cuentaId);

        if (! $cuenta) {
            return '';
        }

        return $this->normalizeAccountEmail((string) $cuenta->correo);
    }

    protected function resolveCuentaIdForPerfil(?Perfil $record): ?int
    {
        if (! $record) {
            return null;
        }

        if (filled($record->cuenta_id)) {
            return (int) $record->cuenta_id;
        }

        $correo = $this->normalizeAccountEmail((string) $record->correo_cuenta);

        if ($correo === '') {
            return null;
        }

        $cuentaId = Cuenta::query()
            ->where('plataforma_id', $this->getRecord()->id)
            ->whereRaw('LOWER(TRIM(correo)) = ?', [$correo])
            ->value('id');

        if (! filled($cuentaId)) {
            $cuentaId = $this->createMissingCuentaFromPerfil($record, $correo);
        }

        return filled($cuentaId) ? (int) $cuentaId : null;
    }

    protected function createMissingCuentaFromPerfil(Perfil $record, string $correoNormalizado): ?int
    {
        if ($correoNormalizado === '') {
            return null;
        }

        $existingId = Cuenta::query()
            ->where('plataforma_id', $this->getRecord()->id)
            ->whereRaw('LOWER(TRIM(correo)) = ?', [$correoNormalizado])
            ->value('id');

        if (filled($existingId)) {
            return (int) $existingId;
        }

        try {
            $cuenta = Cuenta::query()->create([
                'plataforma_id' => $this->getRecord()->id,
                'proveedor' => trim((string) ($record->proveedor_nombre ?: 'Sin proveedor')),
                'correo' => $correoNormalizado,
                'contrasena' => (string) ($record->contrasena_cuenta ?: 'sin-definir'),
                'fecha_inicio' => $record->fecha_inicio?->toDateString() ?: now()->toDateString(),
                'fecha_corte' => $record->fecha_corte?->toDateString() ?: now()->toDateString(),
            ]);
        } catch (QueryException $exception) {
            $existingId = Cuenta::query()
                ->where('plataforma_id', $this->getRecord()->id)
                ->whereRaw('LOWER(TRIM(correo)) = ?', [$correoNormalizado])
                ->value('id');

            return filled($existingId) ? (int) $existingId : null;
        }

        Perfil::query()
            ->where('plataforma_id', $record->plataforma_id)
            ->where('cliente_nombre', $record->cliente_nombre)
            ->whereRaw('LOWER(TRIM(correo_cuenta)) = ?', [$correoNormalizado])
            ->update(['cuenta_id' => $cuenta->id]);

        return (int) $cuenta->id;
    }

    protected function enrichDataWithCuenta(array $data): array
    {
        $cuentaId = (int) ($data['cuenta_id'] ?? 0);

        if ($cuentaId <= 0) {
            throw ValidationException::withMessages([
                'cuenta_id' => 'Selecciona una cuenta válida.',
            ]);
        }

        $cuenta = Cuenta::query()
            ->where('id', $cuentaId)
            ->where('plataforma_id', $this->getRecord()->id)
            ->first();

        if (! $cuenta) {
            throw ValidationException::withMessages([
                'cuenta_id' => 'La cuenta seleccionada no pertenece a esta plataforma.',
            ]);
        }

        $data['cuenta_id'] = $cuenta->id;
        $data['proveedor_nombre'] = $cuenta->proveedor;
        $data['correo_cuenta'] = $this->normalizeAccountEmail((string) $cuenta->correo);
        $data['contrasena_cuenta'] = $cuenta->contrasena;
        $data['fecha_inicio'] = $cuenta->fecha_inicio?->toDateString();
        $data['fecha_corte'] = $cuenta->fecha_corte?->toDateString();

        return $data;
    }

    protected function getCuentaFromId(int $cuentaId): ?Cuenta
    {
        if ($cuentaId <= 0) {
            return null;
        }

        return Cuenta::query()
            ->where('id', $cuentaId)
            ->where('plataforma_id', $this->getRecord()->id)
            ->first();
    }

    protected function getAvailableCuentaOptions(?Perfil $record = null): array
    {
        $cuentas = Cuenta::query()
            ->where('plataforma_id', $this->getRecord()->id)
            ->orderByRaw('LOWER(correo)')
            ->get();

        $excludedIds = null;
        $selectedIds = collect();

        if ($record) {
            $bundle = $this->getClientBundleRecords($record);
            $excludedIds = $bundle->pluck('id');
            $selectedIds = $bundle->pluck('cuenta_id')->filter()->map(fn ($id) => (int) $id);

            if (filled($record->cuenta_id)) {
                $selectedIds->push((int) $record->cuenta_id);
            } else {
                $resolvedCuentaId = $this->resolveCuentaIdForPerfil($record);

                if (filled($resolvedCuentaId)) {
                    $selectedIds->push((int) $resolvedCuentaId);
                }
            }
        }

        $occupancy = $this->buildAccountOccupancyIndex($excludedIds);

        return $cuentas
            ->mapWithKeys(function (Cuenta $cuenta) use ($occupancy, $selectedIds): array {
                $available = $this->getAvailableSlotsForCuenta($cuenta, null, $occupancy);
                $isSelected = $selectedIds->contains((int) $cuenta->id);

                if ($available <= 0 && ! $isSelected) {
                    return [];
                }

                $label = "{$cuenta->correo} ({$cuenta->proveedor}) - Disponibles: {$available}";

                if ($isSelected && $available <= 0) {
                    $label .= ' · Cuenta actual (sin cupos)';
                }

                return [
                    $cuenta->id => $label,
                ];
            })
            ->all();
    }

    protected function getTotalAvailableSlotsAcrossAccounts(int $prioritizedCuentaId, ?Collection $excludedIds = null): int
    {
        $cuentas = $this->getOrderedCuentasForAllocation($prioritizedCuentaId);
        $occupancy = $this->buildAccountOccupancyIndex($excludedIds);

        return $cuentas->sum(fn (Cuenta $cuenta): int => $this->getAvailableSlotsForCuenta($cuenta, null, $occupancy));
    }

    protected function getOrderedCuentasForAllocation(int $prioritizedCuentaId): Collection
    {
        $cuentas = Cuenta::query()
            ->where('plataforma_id', $this->getRecord()->id)
            ->orderByRaw('LOWER(correo)')
            ->get();

        $prioritized = $cuentas->firstWhere('id', $prioritizedCuentaId);

        if (! $prioritized) {
            throw ValidationException::withMessages([
                'cuenta_id' => 'Selecciona una cuenta válida.',
            ]);
        }

        return collect([$prioritized])->merge($cuentas->reject(fn (Cuenta $cuenta): bool => $cuenta->id === $prioritized->id));
    }

    protected function buildAccountOccupancyIndex(?Collection $excludedIds = null, bool $forUpdate = false): array
    {
        $query = Perfil::query()
            ->where('plataforma_id', $this->getRecord()->id)
            ->whereNotNull('correo_cuenta')
            ->where('correo_cuenta', '!=', '');

        if ($excludedIds && $excludedIds->isNotEmpty()) {
            $query->whereNotIn('id', $excludedIds);
        }

        if ($forUpdate) {
            $query->lockForUpdate();
        }

        $rows = $query->get(['correo_cuenta', 'nombre_perfil']);

        return $rows
            ->groupBy(fn (Perfil $perfil): string => $this->normalizeAccountEmail((string) $perfil->correo_cuenta))
            ->map(function (Collection $items): array {
                $slots = $items
                    ->pluck('nombre_perfil')
                    ->map(fn ($perfil): int => (int) $perfil)
                    ->filter(fn (int $slot): bool => $slot > 0)
                    ->unique()
                    ->values();

                return [
                    'total' => $items->count(),
                    'numericSlots' => $slots,
                ];
            })
            ->all();
    }

    protected function getAvailableSlotsForCuenta(Cuenta $cuenta, ?Collection $excludedIds = null, ?array $occupancy = null): int
    {
        $limite = (int) ($this->getRecord()->perfiles_por_cuenta ?: 5);
        $occupancy ??= $this->buildAccountOccupancyIndex($excludedIds);
        $correo = $this->normalizeAccountEmail((string) $cuenta->correo);
        $used = (int) ($occupancy[$correo]['total'] ?? 0);

        return max($limite - $used, 0);
    }

    protected function resolveAssignmentsAcrossAccounts(int $prioritizedCuentaId, int $cantidad, ?Collection $excludedIds = null, bool $forUpdate = false): array
    {
        $limite = (int) ($this->getRecord()->perfiles_por_cuenta ?: 5);
        $cuentas = $this->getOrderedCuentasForAllocation($prioritizedCuentaId);
        $occupancy = $this->buildAccountOccupancyIndex($excludedIds, $forUpdate);

        $assignments = [];

        foreach ($cuentas as $cuenta) {
            $correo = $this->normalizeAccountEmail((string) $cuenta->correo);
            $usedSlots = $occupancy[$correo]['numericSlots'] ?? collect();

            for ($slot = 1; $slot <= $limite; $slot++) {
                if ($usedSlots->contains($slot)) {
                    continue;
                }

                $assignments[] = ['cuenta' => $cuenta, 'slot' => $slot];

                if (count($assignments) === $cantidad) {
                    return $assignments;
                }
            }
        }

        $totalDisponibles = $this->getTotalAvailableSlotsAcrossAccounts($prioritizedCuentaId, $excludedIds);

        throw ValidationException::withMessages([
            'cantidad_perfiles' => "Solamente {$totalDisponibles} perfiles disponibles entre todas las cuentas",
        ]);
    }

    protected function hydratePayloadWithCuentaData(array $data, Cuenta $cuenta): array
    {
        $payload = $data;
        $payload['cuenta_id'] = $cuenta->id;
        $payload['proveedor_nombre'] = $cuenta->proveedor;
        $payload['correo_cuenta'] = $this->normalizeAccountEmail((string) $cuenta->correo);
        $payload['contrasena_cuenta'] = $cuenta->contrasena;
        $payload['fecha_inicio'] = $cuenta->fecha_inicio?->toDateString();
        $payload['fecha_corte'] = $cuenta->fecha_corte?->toDateString();

        return $payload;
    }
}
