<div>
    <button
        x-data="{ dark: $persist(false).as('filament-dark') }"
        @click="dark = !dark; document.documentElement.classList.toggle('dark', dark)"
        class="px-3 py-1 rounded-md text-sm font-medium bg-gray-200 dark:bg-gray-700 dark:text-white"
    >
        <span x-show="!dark">🌙 Oscuro</span>
        <span x-show="dark">☀️ Claro</span>
    </button>
</div>
