<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?string $navigationLabel = 'Roles';
        protected static ?int $navigationSort = 1000;

    protected static function hasAccess(): bool
    {
        $user = auth()->user();

        return $user?->hasRole('administrador') || $user?->can('users.roles.manage');
    }

    public static function permissionLabel(string $permission): string
    {
        return match ($permission) {
            'dashboard.view' => 'Ver panel principal',
            'plataformas.view' => 'Ver plataformas',
            'plataformas.create' => 'Crear plataformas',
            'plataformas.edit' => 'Editar plataformas',
            'plataformas.delete' => 'Eliminar plataformas',
            'clientes.view' => 'Ver clientes',
            'clientes.create' => 'Crear clientes',
            'clientes.edit' => 'Editar clientes',
            'clientes.delete' => 'Eliminar clientes',
            'cuentas.view' => 'Ver cuentas',
            'cuentas.create' => 'Crear cuentas',
            'cuentas.edit' => 'Editar cuentas',
            'cuentas.delete' => 'Eliminar cuentas',
            'users.view' => 'Ver usuarios',
            'users.create' => 'Crear usuarios',
            'users.edit' => 'Editar usuarios',
            'users.delete' => 'Eliminar usuarios',
            'users.roles.manage' => 'Gestionar roles y permisos',
            default => ucfirst(str_replace(['.', '_'], ' ', $permission)),
        };
    }

    protected static function getRoleLabel(?string $roleName): string
    {
        $labels = [
            'administrador' => 'Administrador',
            'manager' => 'Encargado',
        ];

        if (! $roleName) {
            return '-';
        }

        return $labels[$roleName] ?? Str::of($roleName)->replace(['_', '-'], ' ')->headline()->toString();
    }

    public static function canViewAny(): bool
    {
        return static::hasAccess();
    }

    public static function canCreate(): bool
    {
        return static::hasAccess();
    }

    public static function canEdit($record): bool
    {
        if (! static::hasAccess()) {
            return false;
        }

        return $record?->name !== 'administrador';
    }

    public static function canDelete($record): bool
    {
        if (! static::hasAccess()) {
            return false;
        }

        return $record?->name !== 'administrador';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $empresaId = auth()->user()?->empresa_id;

        if (! $empresaId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('empresa_id', $empresaId);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nombre del rol')
                ->required()
                ->maxLength(125),
            Forms\Components\Select::make('permissions')
                ->label('Permisos')
                ->multiple()
                ->preload()
                ->searchable()
                ->options(fn () => Permission::query()
                    ->orderBy('name')
                    ->pluck('name', 'name')
                    ->mapWithKeys(fn (string $name): array => [$name => static::permissionLabel($name)]))
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Rol')
                    ->searchable()
                    ->formatStateUsing(fn (?string $state): string => static::getRoleLabel($state)),
                Tables\Columns\TextColumn::make('permissions.name')
                    ->label('Permisos')
                    ->formatStateUsing(fn (string $state): string => static::permissionLabel($state))
                    ->badge()
                    ->separator(','),
                Tables\Columns\TextColumn::make('created_at')->since(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (Role $record): bool => $record->name !== 'administrador'),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Role $record): bool => $record->name !== 'administrador'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
