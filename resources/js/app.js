import './bootstrap';

document.addEventListener('livewire:initialized', () => {
    Livewire.on('update-subcategoria-options', (event) => {
        const select = document.querySelector('select[name="data.subcategoria_id"]');
        if (select) {
            select.options.length = 0;
            const placeholder = new Option('Seleccione una subcategoría', '', true, true);
            placeholder.disabled = true;
            select.add(placeholder);
            Object.entries(event.options).forEach(([id, name]) => {
                const option = new Option(name, id);
                select.add(option);
            });
            select.dispatchEvent(new Event('change'));
        }
    });
});
