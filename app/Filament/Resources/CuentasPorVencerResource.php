<?php

namespace App\Filament\Resources;

use App\Models\Perfil;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class CuentasPorVencerResource extends Resource
{
    protected static ?string $model = Perfil::class;
    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationLabel = 'Clientes por Vencer';
        protected static ?string $navigationGroup = 'Streaming';
        protected static ?int $navigationSort = 2;

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('row_number')
                    ->label('#')
                    ->getStateUsing(fn ($record, $livewire) => ($livewire->getTableRecords()->search(fn($r) => $r->getKey() === $record->getKey()) + 1)),
                Tables\Columns\TextColumn::make('cliente_nombre')->label('Nombre del cliente')->searchable(),
                Tables\Columns\TextColumn::make('cliente_telefono')->label('Teléfono')->searchable(),
                Tables\Columns\TextColumn::make('plataforma.nombre')->label('Plataforma'),
                Tables\Columns\TextColumn::make('nombre_perfil')->label('Perfil'),
                Tables\Columns\TextColumn::make('proveedor_nombre')->label('Proveedor')->searchable(),
                Tables\Columns\TextColumn::make('correo_cuenta')->label('Correo')->searchable(),
                Tables\Columns\TextColumn::make('contrasena_cuenta')->label('Contraseña'),
                Tables\Columns\TextColumn::make('fecha_caducidad_cuenta')->label('Vence el')->date(),
                Tables\Columns\TextColumn::make('dias_restantes')
                    ->label('Días restantes')
                    ->badge()
                    ->color(fn ($state) => $state === null ? 'gray' : ($state <= 0 ? 'danger' : ($state <= 5 ? 'warning' : 'success')))
                    ->formatStateUsing(fn ($state) => $state === null ? '-' : (string) $state),
                Tables\Columns\TextColumn::make('mensaje')
                    ->label('Mensaje')
                    ->formatStateUsing(fn ($state, $record) =>
                        'Estimado ' . $record->cliente_nombre . ', su cuenta está por vencer, le quedan ' . ($record->dias_restantes ?? '-') . ' días de su suscripción. Por favor envíenos un mensaje si quiere renovar su suscripción.'
                    )
                    ->wrap(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('row_number')
                    ->label('#')
                    ->getStateUsing(fn ($record, $livewire) => ($livewire->getTableRecords()->search(fn($r) => $r->getKey() === $record->getKey()) + 1)),
                Tables\Columns\TextColumn::make('cliente_nombre')->label('Nombre del cliente')->searchable(),
                Tables\Columns\TextColumn::make('cliente_telefono')->label('Teléfono')->searchable(),
                Tables\Columns\TextColumn::make('plataforma.nombre')->label('Plataforma'),
                Tables\Columns\TextColumn::make('nombre_perfil')->label('Perfil'),
                Tables\Columns\TextColumn::make('proveedor_nombre')->label('Proveedor')->searchable(),
                Tables\Columns\TextColumn::make('correo_cuenta')->label('Correo')->searchable(),
                Tables\Columns\TextColumn::make('contrasena_cuenta')->label('Contraseña'),
                Tables\Columns\TextColumn::make('fecha_caducidad_cuenta')->label('Vence el')->date(),
                Tables\Columns\TextColumn::make('dias_restantes')
                    ->label('Días restantes')
                    ->badge()
                    ->color(fn ($state) => $state === null ? 'gray' : ($state <= 0 ? 'danger' : ($state <= 5 ? 'warning' : 'success')))
                    ->formatStateUsing(fn ($state) => $state === null ? '-' : (string) $state),
            ])
            ->actions([
                Tables\Actions\Action::make('mensaje')
                    ->label('Mensaje')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->modalHeading('Mensaje para cliente')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalContent(fn ($record) => view('filament.modals.mensaje-por-vencer', ['record' => $record]))
            ])
            ->filters([
                // Puedes agregar filtros aquí si lo deseas
            ])
            ->defaultSort('fecha_caducidad_cuenta', 'asc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereNotNull('cliente_nombre')
            ->where('cliente_nombre', '!=', '')
            ->whereDate('fecha_caducidad_cuenta', '>=', now())
            ->whereDate('fecha_caducidad_cuenta', '<=', now()->addDays(5))
            ->orderBy('fecha_caducidad_cuenta', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\CuentasPorVencerResource\Pages\ListCuentasPorVencer::route('/'),
        ];
    }
}
