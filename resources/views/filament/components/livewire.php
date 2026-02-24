'components' => [
    'orden-compras-detalle-table' => \App\Filament\Resources\OrdenComprasDetalleTable::class,
],
Forms\Components\Livewire::make(\App\Filament\Resources\OrdenComprasResource\Components\DetallesTable::class, [
    'detalles' => $detalles,
])