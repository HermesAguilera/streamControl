@php
    $textoMensaje = 'Estimado ' . $record->cliente_nombre . ', su cuenta está por vencer, le quedan ' . ($record->dias_restantes ?? '-') . ' días de su suscripción. Por favor envíenos un mensaje si quiere renovar su suscripción.';
@endphp

<div x-data="{ copiado: false }" class="space-y-3">
    <textarea
        x-ref="mensaje"
        readonly
        rows="8"
        style="
            width: 100%;
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            background: #ffffff !important;
            color: #111827 !important;
            opacity: 1 !important;
            padding: 0.75rem;
            line-height: 1.5;
            font-size: 0.95rem;
        "
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
