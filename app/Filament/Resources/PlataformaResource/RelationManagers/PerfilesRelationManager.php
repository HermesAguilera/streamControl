<?php

namespace App\Filament\Resources\PlataformaResource\RelationManagers;

use App\Models\Perfil;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PerfilesRelationManager extends RelationManager
{
    protected static string $relationship = 'perfiles';

    protected static ?string $title = 'Clientes';

    public function table(Table $table): Table
    {
        return $table
            ->recordAction('ver')
            ->columns([
                Tables\Columns\TextColumn::make('cliente_nombre')
                    ->label('Nombre cliente')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cliente_telefono')
                    ->label('Número teléfono')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('proveedor_nombre')
                    ->label('Proveedor')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('correo_cuenta')->label('Correo cuenta')->searchable(),
                Tables\Columns\TextColumn::make('contrasena_cuenta')->label('Contraseña cuenta')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nombre_perfil')->label('Número perfil')->searchable(),
                Tables\Columns\TextColumn::make('pin')->label('PIN')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('fecha_inicio')->label('Fecha inicio')->toggleable(isToggledHiddenByDefault: true)->date(),
                Tables\Columns\TextColumn::make('fecha_corte')->label('Fecha corte')->toggleable(isToggledHiddenByDefault: true)->date(),
                Tables\Columns\TextColumn::make('fecha_caducidad_cuenta')->label('Fecha caducidad')->date(),
                Tables\Columns\TextColumn::make('dias_restantes')
                    ->label('Cuenta regresiva (días)')
                    ->alignment('center')
                    ->badge()
                    ->color(fn ($state) => $state === null ? 'gray' : ($state <= 0 ? 'danger' : ($state <= 5 ? 'warning' : 'success')))
                    ->formatStateUsing(fn ($state) => $state === null ? '-' : (string) $state),
            ])
            ->defaultSort('id', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make('agregarCliente')
                    ->label('Agregar cliente')
                    ->successNotificationTitle('Registro creado correctamente.')
                    ->model(Perfil::class)
                    ->form([
                        Forms\Components\Hidden::make('plataforma_id')
                            ->default(fn (PerfilesRelationManager $livewire) => $livewire->ownerRecord->id),
                        Forms\Components\TextInput::make('cliente_nombre')->label('Nombre cliente')->required()->maxLength(120),
                        Forms\Components\TextInput::make('cliente_telefono')->label('Número teléfono')->required()->maxLength(30),
                        Forms\Components\TextInput::make('proveedor_nombre')->label('Proveedor')->required()->maxLength(120),
                        Forms\Components\TextInput::make('correo_cuenta')->label('Correo cuenta')->required()->email()->maxLength(255),
                        Forms\Components\TextInput::make('contrasena_cuenta')->label('Contraseña cuenta')->required()->maxLength(255),
                        Forms\Components\TextInput::make('nombre_perfil')->label('Número perfil')->required()->maxLength(100),
                        Forms\Components\TextInput::make('pin')->label('PIN')->maxLength(20),
                        Forms\Components\DatePicker::make('fecha_inicio')->label('Fecha inicio')->required(),
                        Forms\Components\DatePicker::make('fecha_corte')->label('Fecha corte')->required(),
                        Forms\Components\DatePicker::make('fecha_caducidad_cuenta')->label('Fecha caducidad')->required(),
                    ])
                    ->using(fn (array $data) => Perfil::create($data)),
            ])
            ->actions([
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
                        ->form([
                            Forms\Components\TextInput::make('cliente_nombre')->label('Nombre cliente')->required()->maxLength(120),
                            Forms\Components\TextInput::make('cliente_telefono')->label('Número teléfono')->required()->maxLength(30),
                            Forms\Components\TextInput::make('proveedor_nombre')->label('Proveedor')->required()->maxLength(120),
                            Forms\Components\TextInput::make('correo_cuenta')->label('Correo cuenta')->required()->email()->maxLength(255),
                            Forms\Components\TextInput::make('contrasena_cuenta')->label('Contraseña cuenta')->required()->maxLength(255),
                            Forms\Components\TextInput::make('nombre_perfil')->label('Número perfil')->required()->maxLength(100),
                            Forms\Components\TextInput::make('pin')->label('PIN')->maxLength(20),
                            Forms\Components\DatePicker::make('fecha_inicio')->label('Fecha inicio')->required(),
                            Forms\Components\DatePicker::make('fecha_corte')->label('Fecha corte')->required(),
                            Forms\Components\DatePicker::make('fecha_caducidad_cuenta')->label('Fecha caducidad')->required(),
                        ]),
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
            ->bulkActions([]);
    }
}
