<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CuentaReportadaResource\Pages;
use App\Models\CuentaReportada;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CuentaReportadaResource extends Resource
{
    protected static ?string $model = CuentaReportada::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $navigationGroup = 'Streaming';
    protected static ?string $navigationLabel = 'Cuentas Reportadas';
    protected static ?int $navigationSort = 3;

    protected static function hasPermission(string $permission): bool
    {
        $user = auth()->user();

        return $user?->hasRole('administrador') || $user?->can($permission);
    }

    public static function canViewAny(): bool
    {
        return static::hasPermission('cuentas_reportadas.view')
            || static::hasPermission('cuentas_reportadas.solve')
            || static::hasPermission('cuentas_reportadas.delete');
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
        return static::hasPermission('cuentas_reportadas.delete');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = CuentaReportada::query()
            ->where('estado', 'en_proceso')
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    protected static function purgeSolvedExpired(): void
    {
        CuentaReportada::query()
            ->where('estado', 'solucionado')
            ->whereNotNull('solucionado_at')
            ->where('solucionado_at', '<=', now()->subHours(12))
            ->delete();
    }

    public static function getEloquentQuery(): Builder
    {
        static::purgeSolvedExpired();

        return parent::getEloquentQuery()->latest('created_at');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordAction('ver')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->alignment('center')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('cuenta')
                    ->label('Cuenta')
                    ->searchable(),
                Tables\Columns\TextColumn::make('numero_perfil')
                    ->label('N° Perfil')
                    ->searchable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripcion')
                    ->wrap()
                    ->limit(80),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->alignment('center')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'solucionado' ? '|' : 'X')
                    ->color(fn (string $state): string => $state === 'solucionado' ? 'success' : 'danger'),
            ])
            ->actions([
                Tables\Actions\Action::make('solucionar')
                    ->label('Solucionar')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (CuentaReportada $record): bool => $record->estado !== 'solucionado' && static::hasPermission('cuentas_reportadas.solve'))
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar solucion')
                    ->modalDescription('El estado cambiara de X a | (solucionado).')
                    ->modalSubmitActionLabel('Solucionar')
                    ->successNotificationTitle('Cuenta marcada como solucionada.')
                    ->action(function (CuentaReportada $record): void {
                        $record->markAsSolved();
                    }),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('ver')
                        ->label('Ver')
                        ->icon('heroicon-o-eye')
                        ->modalHeading('Detalle de la cuenta reportada')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Cerrar')
                        ->infolist([
                            \Filament\Infolists\Components\TextEntry::make('cuenta')->label('Cuenta'),
                            \Filament\Infolists\Components\TextEntry::make('numero_perfil')->label('N° Perfil'),
                            \Filament\Infolists\Components\TextEntry::make('estado')
                                ->label('Estado')
                                ->formatStateUsing(fn (string $state): string => $state === 'solucionado' ? 'Solucionado' : 'En Proceso'),
                            \Filament\Infolists\Components\TextEntry::make('descripcion')->label('Descripcion')->columnSpanFull(),
                            \Filament\Infolists\Components\TextEntry::make('created_at')->label('Fecha de reporte')->dateTime('d M, Y h:i A'),
                            \Filament\Infolists\Components\TextEntry::make('solucionado_at')->label('Fecha de solucion')->dateTime('d M, Y h:i A'),
                        ]),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn (): bool => static::hasPermission('cuentas_reportadas.delete'))
                        ->modalHeading('Confirmar eliminacion')
                        ->modalDescription('Esta accion no se puede deshacer.')
                        ->modalSubmitActionLabel('Eliminar')
                        ->successNotificationTitle('Registro eliminado correctamente.'),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->label(''),
            ])
            ->actionsColumnLabel('Accion')
            ->actionsAlignment('center')
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCuentaReportadas::route('/'),
        ];
    }
}
