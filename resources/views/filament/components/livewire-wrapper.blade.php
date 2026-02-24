@if(isset($component) && isset($params))
    @livewire($component, $params)
@else
    <div class="text-red-500">Error: Componente Livewire no definido correctamente.</div>
@endif
