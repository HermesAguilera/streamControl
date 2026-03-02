<x-filament-panels::page>
    <form wire:submit.prevent="save" class="space-y-6 max-w-3xl">
        {{ $this->form }}

        <div class="flex justify-end">
            <x-filament::button type="submit" icon="heroicon-o-check">
                Guardar ajustes
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
