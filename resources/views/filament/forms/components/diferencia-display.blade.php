{{-- resources/views/filament/forms/components/diferencia-display.blade.php --}}

@props(['sistema', 'diferencia'])

<div class="text-sm space-y-1 mt-1">
    <div class="flex justify-between items-center text-gray-500 dark:text-gray-400">
        <span>Sistema:</span>
        <span class="font-mono font-semibold">L {{ number_format($sistema, 2) }}</span>
    </div>
    <div @class([
        'flex justify-between items-center font-bold',
        'text-danger-600' => $diferencia < 0,
        'text-success-600' => $diferencia > 0,
        'text-gray-600 dark:text-gray-300' => $diferencia == 0,
    ])>
        <span>Diferencia:</span>
        <span class="font-mono">L {{ number_format($diferencia, 2) }}</span>
    </div>
</div>