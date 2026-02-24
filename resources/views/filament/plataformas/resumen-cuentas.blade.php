<div class="mb-4 rounded-xl border border-gray-200 bg-white p-4 dark:border-white/10 dark:bg-gray-900">
    <div class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">
        Resumen de cuentas por correo
    </div>

    @if ($cuentas->isEmpty())
        <div class="text-sm text-gray-500 dark:text-gray-400">
            Aún no hay cuentas registradas para esta plataforma.
        </div>
    @else
        <div class="grid gap-2 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($cuentas as $cuenta)
                <div class="rounded-lg border border-gray-200 px-3 py-2 dark:border-white/10">
                    <div class="mb-1 flex items-center justify-between gap-2">
                        <span class="text-xs font-semibold" style="color: {{ $cuenta['solidColor'] }};">
                            {{ $cuenta['correo'] }}
                        </span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $cuenta['ocupados'] }}/{{ $cuenta['limite'] }}
                        </span>
                    </div>
                    <div class="text-sm">
                        @for ($i = 0; $i < $cuenta['ocupados']; $i++)
                            <span style="display:inline-block;width:10px;height:10px;border-radius:9999px;background:{{ $cuenta['solidColor'] }};margin-right:3px;vertical-align:middle;"></span>
                        @endfor
                        @for ($i = 0; $i < $cuenta['libres']; $i++)
                            <span style="display:inline-block;width:10px;height:10px;border-radius:9999px;background:#d1d5db;margin-right:3px;vertical-align:middle;"></span>
                        @endfor
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
