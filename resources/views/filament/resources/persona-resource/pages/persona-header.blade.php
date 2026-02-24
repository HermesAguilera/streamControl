@props(['record'])

<div class="filament-header space-y-2 items-start justify-between sm:flex sm:space-y-0 sm:space-x-4 sm:rtl:space-x-reverse sm:py-4 p-1 mb-4">
    <div style="display:flex; align-items:center; gap:20px; width:100%;">
        <div style="text-align:center;">
            @if($record && $record->fotografia)
                <img src="{{ asset('storage/' . $record->fotografia) }}" style="width:120px; height:120px; border-radius:50%; object-fit:cover;">
            @else
                <div style="width:120px; height:120px; border-radius:50%; background:#eee; display:flex; align-items:center; justify-content:center; font-size:16px; color:#888;">
                    Sin foto
                </div>
            @endif
        </div>
        <div>
            <h1 class="filament-header-heading text-2xl font-bold tracking-tight">
                {{ $record->primer_nombre ?? '' }} {{ $record->segundo_nombre ?? '' }} {{ $record->primer_apellido ?? '' }} {{ $record->segundo_apellido ?? '' }}
            </h1>
            <p class="text-sm text-gray-500">
                {{ $record->dni ?? 'Sin DNI' }}
            </p>
        </div>
    </div>
</div>
