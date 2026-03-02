@php
    $preferences = \App\Support\UserPreferenceState::forUser(auth()->user());
@endphp

<div class="flex items-center gap-2">
    <a
        href="{{ \App\Filament\Pages\AjustesGenerales::getUrl() }}"
        class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"
    >
        <x-heroicon-o-cog-6-tooth class="h-4 w-4" />
        <span class="hidden sm:inline">Ajustes</span>
    </a>
</div>

<script>
    (() => {
        const preferences = @js($preferences);
        const root = document.documentElement;

        const applyTheme = () => {
            const mode = preferences.theme_mode || 'system';
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            const darkEnabled = mode === 'dark' || (mode === 'system' && prefersDark);

            root.classList.toggle('dark', darkEnabled);
        };

        applyTheme();

        root.classList.toggle('ui-dense', Boolean(preferences.dense_interface));
        root.classList.toggle('ui-reduce-motion', Boolean(preferences.reduced_motion));

        if (window.matchMedia) {
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            const syncSystemTheme = () => {
                if ((preferences.theme_mode || 'system') === 'system') {
                    applyTheme();
                }
            };

            if (typeof mediaQuery.addEventListener === 'function') {
                mediaQuery.addEventListener('change', syncSystemTheme);
            } else if (typeof mediaQuery.addListener === 'function') {
                mediaQuery.addListener(syncSystemTheme);
            }
        }
    })();
</script>
