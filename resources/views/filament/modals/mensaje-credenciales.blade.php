@php
    $textoMensaje = trim((string) ($mensaje ?? 'No se pudo generar el mensaje para este cliente.'));
@endphp

<div x-data="{ copiado: false }" class="space-y-3">
    <textarea
        x-ref="mensaje"
        readonly
        rows="9"
        class="w-full rounded-lg border-gray-300 px-3 py-2 text-sm leading-6"
        style="background-color: #ffffff !important; color: #111827 !important; -webkit-text-fill-color: #111827 !important; opacity: 1 !important;"
    >{{ $textoMensaje }}</textarea>

    <button
        type="button"
        class="fi-btn fi-color-primary fi-size-md fi-btn-size-md rounded-lg px-4 py-2"
        x-on:click="navigator.clipboard.writeText($refs.mensaje.value); copiado = true; setTimeout(() => copiado = false, 1500)"
    >
        Copiar mensaje
    </button>

    <p x-show="copiado" x-transition class="text-sm text-success-600">
        Mensaje copiado al portapapeles.
    </p>
</div>
