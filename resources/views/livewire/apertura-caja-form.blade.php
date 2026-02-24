<div>
    <h2>Apertura de Caja</h2>
    <form wire:submit.prevent="aperturar">
        <div>
            <label>Monto Inicial</label>
            <input type="number" step="0.01" wire:model="monto_inicial" required>
        </div>
        <div>
            <label>Empleado</label>
            <input type="text" value="{{ $empleado->nombre ?? $empleado->name ?? '' }}" readonly>
        </div>
        <button type="submit">Aperturar</button>
    </form>
    @if(session()->has('success'))
        <div>{{ session('success') }}</div>
    @endif
</div>
