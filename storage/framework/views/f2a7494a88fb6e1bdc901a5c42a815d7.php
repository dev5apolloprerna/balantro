<script>
    window.initLazySelect2 = window.initLazySelect2 || function(selector, options) {
        const initOne = function(element, openAfterInit) {
            const $element = $(element);
            if (!$element.length || $element.hasClass('select2-hidden-accessible')) {
                if (openAfterInit && $element.hasClass('select2-hidden-accessible')) {
                    $element.select2('open');
                }
                return;
            }
            $element.select2(options || {});
            if (openAfterInit) {
                $element.select2('open');
            }
        };

        $(document).on('focus mouseenter', selector, function(event) {
            initOne(this, event.type === 'focus');
        });
    };
</script><?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/partials/lazy-select2.blade.php ENDPATH**/ ?>