<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Role;
use App\Models\User;
use Filament\Forms\Get;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?string $navigationLabel = 'Usuarios';
        protected static ?int $navigationSort = 999;

    protected static function hasPermission(string $permission): bool
    {
        $user = auth()->user();

        return $user?->hasRole('administrador') || $user?->can($permission);
    }

    protected static function canManageRoles(): bool
    {
        return static::hasPermission('users.roles.manage');
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
        return static::hasPermission('users.view')
            || static::hasPermission('users.create')
            || static::hasPermission('users.edit')
            || static::hasPermission('users.delete');
    }

    public static function canCreate(): bool
    {
        return static::hasPermission('users.create');
    }

    public static function canEdit($record): bool
    {
        return static::hasPermission('users.edit');
    }

    public static function canDelete($record): bool
    {
        return static::hasPermission('users.delete');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\TextInput::make('email')->email()->required()->maxLength(255),
            Forms\Components\TextInput::make('password')
                ->password()
                ->maxLength(255)
                ->required(fn ($livewire) => $livewire instanceof Pages\CreateUser)
                ->dehydrated(fn (Get $get) => filled($get('password'))),
            Forms\Components\Select::make('roles')
                ->label('Roles')
                ->relationship('roles', 'name', modifyQueryUsing: fn (Builder $query) => $query
                    ->where('empresa_id', auth()->user()?->empresa_id))
                ->getOptionLabelFromRecordUsing(fn ($record): string => static::getRoleLabel($record->name))
                ->multiple()
                ->preload()
                ->searchable()
                ->visible(fn () => static::canManageRoles()),
        ])->columns([
            'default' => 1,
            'md' => 2,
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('email')->searchable(),
            Tables\Columns\TextColumn::make('roles.name')
                ->label('Roles')
                ->badge()
                ->separator(',')
                ->formatStateUsing(fn (?string $state): string => static::getRoleLabel($state)),
            Tables\Columns\TextColumn::make('created_at')->since(),
        ])->actions([
            Tables\Actions\Action::make('cambiarRol')
                ->label('Cambiar rol')
                ->icon('heroicon-o-shield-check')
                ->visible(fn () => static::canManageRoles())
                ->form([
                    Forms\Components\Select::make('roles')
                        ->label('Roles')
                        ->multiple()
                        ->required()
                        ->preload()
                        ->options(fn () => Role::query()
                            ->where('empresa_id', auth()->user()?->empresa_id)
                            ->get()
                            ->mapWithKeys(fn ($role) => [
                                $role->name => static::getRoleLabel($role->name),
                            ])),
                ])
                ->fillForm(fn (User $record): array => [
                    'roles' => $record->roles()->pluck('name')->all(),
                ])
                ->action(function (User $record, array $data): void {
                    $record->syncRoles($data['roles']);
                }),
            Tables\Actions\EditAction::make(),
        ])
            ->actionsColumnLabel('Acción')
            ->actionsAlignment('center')
            ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
