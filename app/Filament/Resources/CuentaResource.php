<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CuentaResource\Pages;
use App\Models\Cuenta;
use Filament\Forms;
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
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('proveedor')
                    ->label('Proveedor')
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
