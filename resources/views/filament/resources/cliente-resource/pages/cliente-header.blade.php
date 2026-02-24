@props(['record', 'persona'])

<div class="filament-header space-y-2 items-start justify-between sm:flex sm:space-y-0 sm:space-x-4 sm:rtl:space-x-reverse sm:py-4 p-1 mb-4">
    <div style="display:flex; align-items:center; gap:20px; width:100%;">
        <div style="text-align:center;">
            @if($persona && $persona->fotografia)
                <img src="{{ asset('storage/' . $persona->fotografia) }}" style="width:120px; height:120px; border-radius:50%; object-fit:cover;">
            @else
                <div style="width:120px; height:120px; border-radius:50%; background:#eee; display:flex; align-items:center; justify-content:center; font-size:16px; color:#888;">
                    Sin foto
                </div>
            @endif
        </div>
        <div>
            <h1 class="filament-header-heading text-2xl font-bold tracking-tight">
                {{ $persona->primer_nombre ?? '' }} {{ $persona->segundo_nombre ?? '' }} {{ $persona->primer_apellido ?? '' }} {{ $persona->segundo_apellido ?? '' }}
            </h1>
            <p class="text-sm text-gray-500">
                {{ $persona->dni ?? 'Sin DNI' }}
            </p>
        </div>
    </div>
</div>
