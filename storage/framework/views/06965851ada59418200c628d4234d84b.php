<script>
    window.hydrateDeferredSelectOptions = window.hydrateDeferredSelectOptions || function(selector) {
        const hydrate = function(element) {
            const select = element instanceof HTMLElement ? element : document.querySelector(element);
            if (!select || select.dataset.optionsHydrated === '1') return;

            const sourceName = select.dataset.optionsSource;
            const options = Array.isArray(window[sourceName]) ? window[sourceName] : [];
            const selected = String(select.dataset.selected || select.value || '').trim();
            const placeholder = select.dataset.placeholder || 'Select';
            const fragment = document.createDocumentFragment();

            const blank = document.createElement('option');
            blank.value = '';
            blank.textContent = placeholder;
            fragment.appendChild(blank);

            options.forEach(function(optionValue) {
                const value = String(optionValue || '').trim();
                if (!value) return;
                const option = document.createElement('option');
                option.value = value;
                option.textContent = value;
                if (selected && value.toLowerCase() === selected.toLowerCase()) {
                    option.selected = true;
                }
                fragment.appendChild(option);
            });

            select.replaceChildren(fragment);
            if (selected && !Array.from(select.options).some(option => option.selected && option.value.toLowerCase() === selected.toLowerCase())) {
                const option = new Option(selected, selected, true, true);
                select.appendChild(option);
            }
            select.dataset.optionsHydrated = '1';
        };

        document.querySelectorAll(selector).forEach(function(select) {
            select.addEventListener('focus', function() { hydrate(select); }, { once: true });
            select.addEventListener('mousedown', function() { hydrate(select); }, { once: true });
        });
    };
</script><?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/partials/deferred-select-options.blade.php ENDPATH**/ ?>