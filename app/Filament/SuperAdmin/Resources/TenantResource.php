<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\TenantResource\Pages;
use App\Models\Departamento;
use App\Models\Empresa;
use App\Models\Municipio;
use App\Models\Paises;
use App\Models\Tenant;
use App\Services\Tenancy\TenantHealthCheckService;
use App\Services\Tenancy\ProvisionTenantService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Empresas';

    protected static ?string $modelLabel = 'Empresa';

    protected static ?string $pluralModelLabel = 'Empresas';

    protected static ?string $navigationGroup = 'Provisioning';

    public static function canViewAny(): bool
    {
        return auth()->user()?->is_super_admin === true;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->is_super_admin === true;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->is_super_admin === true;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Empresa')
                ->schema([
                    Forms\Components\TextInput::make('company_name')
                        ->label('Nombre de la empresa')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->alphaDash()
                        ->unique(table: Tenant::class, column: 'slug', ignoreRecord: true)
                        ->maxLength(100),
                    Forms\Components\TextInput::make('direccion')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('telefono')
                        ->maxLength(20),
                    Forms\Components\Select::make('pais_id')
                        ->options(fn (): array => Paises::query()->pluck('nombre_pais', 'id')->all())
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('departamento_id')
                        ->options(fn (): array => Departamento::query()->pluck('nombre_departamento', 'id')->all())
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('municipio_id')
                        ->options(fn (): array => Municipio::query()->pluck('nombre_municipio', 'id')->all())
                        ->required()
                        ->searchable()
                        ->preload(),
                ])
                ->columns(2),
            Forms\Components\Section::make('Base de datos de la empresa')
                ->schema([
                    Forms\Components\Select::make('db_driver')
                        ->required()
                        ->default(config('tenancy.tenant_default_driver'))
                        ->options([
                            'mysql' => 'MySQL',
                            'pgsql' => 'PostgreSQL',
                        ]),
                    Forms\Components\TextInput::make('database_name')
                        ->label('Nombre de BD')
                        ->helperText('Opcional. Si se omite, se genera automaticamente con prefijo de empresa.')
                        ->maxLength(64),
                    Forms\Components\TextInput::make('db_host')->required()->default(config('database.connections.'.config('database.default').'.host')),
                    Forms\Components\TextInput::make('db_port')->required()->numeric()->default((int) config('database.connections.'.config('database.default').'.port')),
                ])
                ->columns(2),
            Forms\Components\Section::make('Primer Admin de Empresa')
                ->schema([
                    Forms\Components\TextInput::make('admin_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('admin_email')
                        ->email()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('admin_password')
                        ->label('Contrasena del admin de empresa')
                        ->helperText('Este valor es visible para permitir su edicion directa desde SuperAdmin.')
                        ->required(fn (string $operation): bool => $operation === 'create'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Empresa')->searchable(),
                Tables\Columns\TextColumn::make('slug')->searchable(),
                Tables\Columns\BadgeColumn::make('status'),
                Tables\Columns\BadgeColumn::make('salud')
                    ->label('Salud')
                    ->getStateUsing(fn (Tenant $record): string => app(TenantHealthCheckService::class)->check($record)['status'])
                    ->colors([
                        'success' => 'ok',
                        'warning' => 'warning',
                        'danger' => 'error',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'ok' => 'OK',
                        'warning' => 'Advertencia',
                        default => 'Error',
                    }),
                Tables\Columns\TextColumn::make('db_driver')->label('Motor DB'),
                Tables\Columns\TextColumn::make('db_database')->label('Base de datos'),
                Tables\Columns\TextColumn::make('provisioned_at')->label('Provisionada')->since(),
                Tables\Columns\TextColumn::make('created_at')->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('diagnostico')
                    ->label('Diagnostico')
                    ->icon('heroicon-o-heart')
                    ->color('gray')
                    ->modalHeading('Diagnostico de infraestructura')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->form([
                        Forms\Components\Textarea::make('health_message')
                            ->label('Resultado')
                            ->readOnly()
                            ->rows(5)
                            ->default(function (Tenant $record): string {
                                $report = app(TenantHealthCheckService::class)->check($record);

                                return $report['message'];
                            })
                            ->dehydrated(false),
                        Forms\Components\TagsInput::make('missing_tables')
                            ->label('Tablas faltantes')
                            ->default(function (Tenant $record): array {
                                $report = app(TenantHealthCheckService::class)->check($record);

                                return $report['missing_tables'];
                            })
                            ->dehydrated(false)
                            ->disabled(),
                    ]),
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil-square'),
                Tables\Actions\Action::make('viewDetails')
                    ->label('Ver datos')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading('Datos registrados de la empresa')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label('Empresa')
                            ->readOnly()
                            ->default(fn (Tenant $record): string => $record->name)
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->readOnly()
                            ->default(fn (Tenant $record): string => $record->slug)
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('db_driver')
                            ->label('Motor DB')
                            ->readOnly()
                            ->default(fn (Tenant $record): string => $record->db_driver)
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('db_host')
                            ->label('DB Host')
                            ->readOnly()
                            ->default(fn (Tenant $record): string => $record->db_host)
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('db_port')
                            ->label('DB Port')
                            ->readOnly()
                            ->default(fn (Tenant $record): string => (string) $record->db_port)
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('db_database')
                            ->label('DB Database')
                            ->readOnly()
                            ->default(fn (Tenant $record): string => $record->db_database)
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('db_username')
                            ->label('DB Username')
                            ->readOnly()
                            ->default(fn (Tenant $record): string => $record->db_username)
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('db_password')
                            ->label('DB Password')
                            ->password()
                            ->revealable()
                            ->readOnly()
                            ->default(fn (Tenant $record): ?string => $record->db_password)
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('bootstrap_admin_name')
                            ->label('Admin inicial (nombre)')
                            ->readOnly()
                            ->default(fn (Tenant $record): ?string => $record->bootstrap_admin_name)
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('bootstrap_admin_email')
                            ->label('Admin inicial (correo)')
                            ->readOnly()
                            ->default(fn (Tenant $record): ?string => $record->bootstrap_admin_email)
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('bootstrap_admin_password')
                            ->label('Admin inicial (contrasena)')
                            ->password()
                            ->revealable()
                            ->readOnly()
                            ->default(fn (Tenant $record): ?string => $record->bootstrap_admin_password)
                            ->dehydrated(false),
                    ]),
                Tables\Actions\Action::make('viewProvisioningError')
                    ->label('Ver error')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->visible(fn (Tenant $record): bool => $record->status === 'failed' && filled($record->provisioning_error))
                    ->modalHeading('Detalle del error de aprovisionamiento')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->form([
                        Forms\Components\Textarea::make('provisioning_error')
                            ->label('Error')
                            ->readOnly()
                            ->rows(12)
                            ->default(fn (Tenant $record): ?string => $record->provisioning_error)
                            ->dehydrated(false),
                    ]),
                Tables\Actions\Action::make('retryProvisioning')
                    ->label('Reintentar')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (Tenant $record): bool => $record->status === 'failed')
                    ->requiresConfirmation()
                    ->modalHeading('Reintentar aprovisionamiento')
                    ->modalDescription('Se recreara la base de datos de la empresa y se intentara aprovisionar de nuevo.')
                    ->form([
                        Forms\Components\TextInput::make('admin_name')
                            ->label('Nombre del Admin de Empresa')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('admin_email')
                            ->label('Correo del Admin de Empresa')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('admin_password')
                            ->label('Contrasena del Admin de Empresa')
                            ->password()
                            ->revealable()
                            ->required(),
                    ])
                    ->action(function (Tenant $record, array $data): void {
                        /** @var ProvisionTenantService $provisioner */
                        $provisioner = app(ProvisionTenantService::class);

                        $provisioner->retryFailedProvisioning($record, auth()->user(), $data);

                        Notification::make()
                            ->title('Aprovisionamiento reintentado correctamente')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
