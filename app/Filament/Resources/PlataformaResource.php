<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlataformaResource\Pages;
use App\Filament\Resources\PlataformaResource\RelationManagers\PerfilesRelationManager;
use App\Models\Plataforma;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlataformaResource extends Resource
{
    protected static ?string $model = Plataforma::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationGroup = 'Streaming';
    protected static ?string $navigationLabel = 'Plataformas';
    protected static ?int $navigationSort = 1;

    protected static function hasPermission(string $permission): bool
    {
        $user = auth()->user();

        return $user?->hasRole('administrador')
            || $user?->hasRole('admin_empresa')
            || $user?->can($permission);
    }

    public static function canViewAny(): bool
    {
        return static::hasPermission('plataformas.view')
            || static::hasPermission('plataformas.create')
            || static::hasPermission('plataformas.edit')
            || static::hasPermission('plataformas.delete')
            || static::hasPermission('clientes.view')
            || static::hasPermission('clientes.create')
            || static::hasPermission('clientes.edit')
            || static::hasPermission('clientes.delete');
    }

    public static function canCreate(): bool
    {
        return static::hasPermission('plataformas.create');
    }

    public static function canEdit($record): bool
    {
        return static::hasPermission('plataformas.edit');
    }

    public static function canDelete($record): bool
    {
        return static::hasPermission('plataformas.delete');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nombre')->required()->maxLength(100),
            Forms\Components\Textarea::make('descripcion')->columnSpanFull(),
            Forms\Components\TextInput::make('perfiles_por_cuenta')
                ->label('Perfiles por cuenta')
                ->numeric()
                ->minValue(1)
                ->maxValue(20)
                ->default(5)
                ->required(),
            Forms\Components\Toggle::make('activa')->default(true)->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('nombre')->searchable(),
            Tables\Columns\IconColumn::make('activa')->boolean(),
            Tables\Columns\TextColumn::make('perfiles_por_cuenta')->label('Perfiles/cuenta'),
            Tables\Columns\TextColumn::make('perfiles_count')->counts('perfiles')->label('Perfiles'),
            Tables\Columns\TextColumn::make('created_at')->since(),
        ])->actions([
            Tables\Actions\ActionGroup::make([
                Tables\Actions\Action::make('clientes')
                    ->label('Clientes')
                    ->icon('heroicon-o-users')
                    ->visible(fn () => static::hasPermission('clientes.view'))
                    ->url(fn (Plataforma $record): string => static::getUrl('clientes', ['record' => $record])),
                Tables\Actions\EditAction::make(),
            ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->label(''),
        ])
            ->actionsColumnLabel('Acción')
            ->actionsAlignment('center')
            ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            PerfilesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlataformas::route('/'),
            'create' => Pages\CreatePlataforma::route('/create'),
            'clientes' => Pages\GestionClientesPlataforma::route('/{record}/clientes'),
            'edit' => Pages\EditPlataforma::route('/{record}/edit'),
        ];
    }
}
