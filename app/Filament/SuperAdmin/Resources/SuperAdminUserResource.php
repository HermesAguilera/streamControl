<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\SuperAdminUserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SuperAdminUserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationLabel = 'Usuarios SuperAdmin';

    protected static ?string $modelLabel = 'usuario superadmin';

    protected static ?string $pluralModelLabel = 'usuarios superadmin';

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
        return auth()->user()?->is_super_admin === true;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('is_super_admin', true);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Datos de acceso')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label('Correo')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\TextInput::make('password')
                        ->label('Contrasena')
                        ->password()
                        ->revealable()
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->dehydrated(fn ($state): bool => filled($state))
                        ->maxLength(255),
                    Forms\Components\Hidden::make('is_super_admin')
                        ->default(true),
                ])
                ->columns(2),
            Forms\Components\Section::make('Asignaciones requeridas')
                ->schema([
                    Forms\Components\Select::make('empresa_id')
                        ->label('Empresa base')
                        ->relationship('empresa', 'nombre')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('persona_id')
                        ->label('Persona (opcional)')
                        ->relationship('persona', 'primer_nombre')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nombre')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Correo')->searchable(),
                Tables\Columns\TextColumn::make('empresa.nombre')->label('Empresa base')->searchable(),
                Tables\Columns\IconColumn::make('is_super_admin')->label('SuperAdmin')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->since(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn (User $record): bool => $record->id !== auth()->id())
                        ->modalHeading('Confirmar eliminacion')
                        ->modalDescription('Esta accion no se puede deshacer.')
                        ->modalSubmitActionLabel('Eliminar'),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->label(''),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuperAdminUsers::route('/'),
            'create' => Pages\CreateSuperAdminUser::route('/create'),
            'edit' => Pages\EditSuperAdminUser::route('/{record}/edit'),
        ];
    }
}
