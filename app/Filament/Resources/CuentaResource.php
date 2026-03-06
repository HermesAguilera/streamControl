<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CuentaResource\Pages;
use App\Models\Cuenta;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CuentaResource extends Resource
{
    protected static ?string $model = Cuenta::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationGroup = 'Streaming';
    protected static ?string $navigationLabel = 'Cuentas';
    protected static ?int $navigationSort = 2;

    protected static function hasPermission(string $permission): bool
    {
        $user = auth()->user();

        return $user?->hasRole('administrador') || $user?->can($permission);
    }

    public static function canViewAny(): bool
    {
        return static::hasPermission('cuentas.view')
            || static::hasPermission('cuentas.create')
            || static::hasPermission('cuentas.edit')
            || static::hasPermission('cuentas.delete');
    }

    public static function canCreate(): bool
    {
        return static::hasPermission('cuentas.create');
    }

    public static function canEdit($record): bool
    {
        return static::hasPermission('cuentas.edit');
    }

    public static function canDelete($record): bool
    {
        return static::hasPermission('cuentas.delete');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('plataforma_id')
                ->label('Plataforma')
                ->relationship('plataforma', 'nombre')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\TextInput::make('proveedor')
                ->label('Proveedor')
                ->required()
                ->maxLength(120),
            Forms\Components\TextInput::make('correo')
                ->label('Correo')
                ->email()
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('contrasena')
                ->label('Contraseña')
                ->password()
                ->revealable()
                ->required()
                ->maxLength(255),
            Forms\Components\DatePicker::make('fecha_inicio')
                ->label('Fecha de inicio')
                ->required(),
            Forms\Components\DatePicker::make('fecha_corte')
                ->label('Fecha de corte')
                ->required(),
            Forms\Components\TextInput::make('cantidad_perfiles')
                ->label('Cantidad de Perfiles')
                ->numeric()
                ->minValue(1)
                ->maxValue(20)
                ->default(5)
                ->required()
                ->live(debounce: 200)
                ->dehydrated(false)
                ->afterStateHydrated(function (?Cuenta $record, Set $set, ?string $state): void {
                    $cantidad = (int) ($state ?: 0);

                    if ($cantidad <= 0) {
                        $cantidad = $record?->configuracionPerfiles()?->count() ?: 5;
                        $set('cantidad_perfiles', $cantidad);
                    }

                    $set('configuracionPerfiles', static::buildProfileConfigState($cantidad, $record?->configuracionPerfiles?->toArray() ?: []));
                })
                ->afterStateUpdated(function ($state, Get $get, Set $set): void {
                    $cantidad = max((int) $state, 1);
                    $actual = $get('configuracionPerfiles');

                    $set('configuracionPerfiles', static::buildProfileConfigState($cantidad, is_array($actual) ? $actual : []));
                }),
            Forms\Components\Repeater::make('configuracionPerfiles')
                ->label('Perfiles de la Cuenta')
                ->relationship('configuracionPerfiles')
                ->schema([
                    Forms\Components\Hidden::make('numero_perfil')
                        ->required()
                        ->dehydrated(),
                    Forms\Components\Placeholder::make('perfil_label')
                        ->label('Perfil')
                        ->content(fn (Get $get): string => 'Perfil ' . ((int) ($get('numero_perfil') ?: 0))),
                    Forms\Components\TextInput::make('pin')
                        ->label('PIN')
                        ->maxLength(20),
                ])
                ->columns(2)
                ->addable(false)
                ->deletable(false)
                ->reorderable(false)
                ->columnSpanFull(),
        ]);
    }

    protected static function buildProfileConfigState(int $cantidad, array $currentState): array
    {
        $cantidad = max($cantidad, 1);

        $rowsBySlot = collect($currentState)
            ->mapWithKeys(function ($item): array {
                $slot = (int) ($item['numero_perfil'] ?? 0);

                if ($slot <= 0) {
                    return [];
                }

                return [$slot => [
                    'id' => $item['id'] ?? null,
                    'pin' => $item['pin'] ?? null,
                ]];
            });

        $rows = [];

        for ($slot = 1; $slot <= $cantidad; $slot++) {
            $existing = $rowsBySlot->get($slot, []);

            $rows[] = [
                'id' => $existing['id'] ?? null,
                'numero_perfil' => $slot,
                'pin' => $existing['pin'] ?? null,
            ];
        }

        return $rows;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('proveedor')
                    ->label('Proveedor')
                    ->action(fn (Cuenta $record, $livewire) => $livewire->mountTableAction('ver', (string) $record->getKey()))
                    ->searchable(),
                Tables\Columns\TextColumn::make('correo')
                    ->label('Correo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('plataforma.nombre')
                    ->label('Plataforma')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Fecha inicio')
                    ->date(),
                Tables\Columns\TextColumn::make('fecha_corte')
                    ->label('Fecha corte')
                    ->date(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('ver')
                        ->label('Ver')
                        ->icon('heroicon-o-eye')
                        ->modalHeading('Detalle de la cuenta')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Cerrar')
                        ->modalContent(fn (Cuenta $record) => view('filament.modals.detalle-cuenta', [
                            'cuenta' => $record->loadMissing('plataforma'),
                        ])),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->modalHeading('Confirmar eliminación')
                        ->modalDescription('Esta acción no se puede deshacer.')
                        ->modalSubmitActionLabel('Eliminar')
                        ->successNotificationTitle('Registro eliminado correctamente.'),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->label(''),
            ])
                    ->actionsColumnLabel('Acción')
                    ->actionsAlignment('center')
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->modalHeading('Confirmar eliminación masiva')
                    ->modalDescription('Se eliminarán los registros seleccionados y esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Eliminar seleccionados')
                    ->successNotificationTitle('Registros eliminados correctamente.'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCuentas::route('/'),
            'create' => Pages\CreateCuenta::route('/create'),
            'edit' => Pages\EditCuenta::route('/{record}/edit'),
        ];
    }
}
