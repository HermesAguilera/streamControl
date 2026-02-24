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
            ->columns([
                Tables\Columns\TextColumn::make('cliente_nombre')->label('Nombre cliente')->searchable(),
                Tables\Columns\TextColumn::make('cliente_telefono')->label('Número teléfono')->searchable(),
                Tables\Columns\TextColumn::make('proveedor_nombre')->label('Proveedor')->searchable(),
                Tables\Columns\TextColumn::make('correo_cuenta')->label('Correo cuenta')->searchable(),
                Tables\Columns\TextColumn::make('contrasena_cuenta')->label('Contraseña cuenta')->toggleable(),
                Tables\Columns\TextColumn::make('nombre_perfil')->label('Número perfil')->searchable(),
                Tables\Columns\TextColumn::make('pin')->label('PIN'),
                Tables\Columns\TextColumn::make('fecha_inicio')->label('Fecha inicio')->date(),
                Tables\Columns\TextColumn::make('fecha_corte')->label('Fecha corte')->date(),
                Tables\Columns\TextColumn::make('fecha_caducidad_cuenta')->label('Fecha caducidad')->date(),
                Tables\Columns\TextColumn::make('dias_restantes')
                    ->label('Cuenta regresiva (días)')
                    ->badge()
                    ->color(fn ($state) => $state === null ? 'gray' : ($state <= 0 ? 'danger' : ($state <= 5 ? 'warning' : 'success')))
                    ->formatStateUsing(fn ($state) => $state === null ? '-' : (string) $state),
            ])
            ->defaultSort('id', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make('agregarCliente')
                    ->label('Agregar cliente')
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
                        Forms\Components\Select::make('estado')
                            ->options([
                                'disponible' => 'Disponible',
                                'activo' => 'Activo',
                                'vencido' => 'Vencido',
                                'suspendido' => 'Suspendido',
                            ])
                            ->default('activo')
                            ->required(),
                        Forms\Components\Toggle::make('disponible')->required()->default(false),
                        Forms\Components\Textarea::make('notas')->columnSpanFull(),
                    ])
                    ->using(fn (array $data) => Perfil::create($data)),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
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
                        Forms\Components\Select::make('estado')
                            ->options([
                                'disponible' => 'Disponible',
                                'activo' => 'Activo',
                                'vencido' => 'Vencido',
                                'suspendido' => 'Suspendido',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('disponible')->required(),
                        Forms\Components\Textarea::make('notas')->columnSpanFull(),
                    ]),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([]);
    }
}
