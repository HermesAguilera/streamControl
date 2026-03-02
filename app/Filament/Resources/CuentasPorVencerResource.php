<?php

namespace App\Filament\Resources;

use App\Models\Perfil;
use App\Support\ClientMessageBuilder;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CuentasPorVencerResource extends Resource
{
    protected static ?string $model = Perfil::class;

    protected static ?string $navigationLabel = 'Clientes por vencer';

    protected static ?string $navigationGroup = 'Streaming';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'cliente por vencer';

    protected static ?string $pluralModelLabel = 'clientes por vencer';

    protected static function baseExpiryQuery(): Builder
    {
        return Perfil::query()
            ->whereNotNull('cliente_nombre')
            ->where('cliente_nombre', '!=', '')
            ->whereNotNull('fecha_caducidad_cuenta')
            ->whereDate('fecha_caducidad_cuenta', '>=', now()->toDateString())
            ->whereDate('fecha_caducidad_cuenta', '<', now()->addDays(3)->toDateString());
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::baseExpiryQuery()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $hasExpiringToday = static::baseExpiryQuery()
            ->whereDate('fecha_caducidad_cuenta', now()->toDateString())
            ->exists();

        return $hasExpiringToday ? 'danger' : 'warning';
    }

    public static function getNavigationIcon(): string
    {
        $count = static::baseExpiryQuery()->count();

        if ($count === 0) {
            return 'heroicon-o-bell';
        }

        $hasExpiringToday = static::baseExpiryQuery()
            ->whereDate('fecha_caducidad_cuenta', now()->toDateString())
            ->exists();

        return $hasExpiringToday
            ? 'heroicon-o-exclamation-triangle'
            : 'heroicon-o-bell-alert';
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cliente_posicion')
                    ->label('#')
                    ->alignment('center')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('cliente_nombre')->label('Nombre del cliente')->searchable(),
                Tables\Columns\TextColumn::make('cliente_telefono')
                    ->label('Teléfono')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('plataforma.nombre')->label('Plataforma'),
                Tables\Columns\TextColumn::make('nombre_perfil')
                    ->label('Perfil')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('proveedor_nombre')
                    ->label('Proveedor')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('correo_cuenta')
                    ->label('Correo')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('fecha_caducidad_cuenta')
                    ->label('Vence el')
                    ->alignment('center')
                    ->date(),
                Tables\Columns\TextColumn::make('dias_restantes')
                    ->label('Días restantes')
                    ->alignment('center')
                    ->badge()
                    ->color(fn ($state) => $state === null ? 'gray' : ($state <= 0 ? 'danger' : 'warning'))
                    ->formatStateUsing(fn ($state) => $state === null ? '-' : (string) $state),
                Tables\Columns\TextColumn::make('mensaje')
                    ->label('Plantilla sugerida')
                    ->limit(90)
                    ->formatStateUsing(fn ($state, Perfil $record) =>
                        ClientMessageBuilder::buildExpiryMessage($record)
                    )
                    ->tooltip(fn (Perfil $record): string => ClientMessageBuilder::buildExpiryMessage($record))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap(),
            ])
            ->actions([
                Tables\Actions\Action::make('mensaje')
                    ->label('Mensaje')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color(fn (Perfil $record): string => ((int) ($record->dias_restantes ?? 99)) === 0 ? 'danger' : 'primary')
                    ->modalHeading(fn (Perfil $record): string => ((int) ($record->dias_restantes ?? 99)) === 0
                        ? 'Mensaje de alerta: vence hoy'
                        : 'Mensaje de recordatorio')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalContent(fn (Perfil $record) => view('filament.modals.mensaje-por-vencer', [
                        'mensaje' => ClientMessageBuilder::buildExpiryMessage($record),
                    ])),
            ])
                    ->actionsColumnLabel('Acción')
                    ->actionsAlignment('center')
            ->defaultSort('fecha_caducidad_cuenta', 'asc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('plataforma')
            ->whereNotNull('fecha_caducidad_cuenta')
            ->whereNotNull('cliente_nombre')
            ->where('cliente_nombre', '!=', '')
            ->whereDate('fecha_caducidad_cuenta', '>=', now()->toDateString())
            ->whereDate('fecha_caducidad_cuenta', '<', now()->addDays(3)->toDateString())
            ->orderBy('fecha_caducidad_cuenta', 'asc');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\CuentasPorVencerResource\Pages\ListCuentasPorVencer::route('/'),
        ];
    }
}
