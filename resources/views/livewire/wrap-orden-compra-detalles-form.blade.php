@php
    $ordenId = $record->id ?? null;
@endphp

@livewire('orden-compra-detalles-form', ['ordenId' => $ordenId], key('orden-'.$ordenId))


