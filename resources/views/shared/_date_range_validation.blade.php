<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dateRangePairs = [
            ['start_date', 'end_date'],
            ['from_date', 'to_date'],
            ['from_custom', 'to_custom'],
        ];

        document.querySelectorAll('form').forEach((form) => {
            dateRangePairs.forEach(([fromName, toName]) => {
                const fromInput = form.querySelector(`[name="${fromName}"]`);
                const toInput = form.querySelector(`[name="${toName}"]`);

                if (!fromInput || !toInput) return;

                const syncLimits = () => {
                    if (fromInput.value) {
                        toInput.min = fromInput.value;
                    } else {
                        toInput.min = toInput.dataset.originalMin || '1900-01-01';
                    }

                    if (toInput.value) {
                        fromInput.max = toInput.value;
                    } else {
                        fromInput.max = fromInput.dataset.originalMax || '2099-12-31';
                    }
                };

                fromInput.dataset.originalMax = fromInput.max;
                toInput.dataset.originalMin = toInput.min;
                fromInput.addEventListener('change', syncLimits);
                toInput.addEventListener('change', syncLimits);
                syncLimits();
            });
        });
    });
</script>