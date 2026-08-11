<?php
    $displayText = filled($text) ? $text : ($fallback ?? '—');
?>

<button type="button"
    data-expandable-table-text
    aria-expanded="false"
    onclick="const expanded = this.getAttribute('aria-expanded') === 'true'; const text = this.querySelector('span'); this.setAttribute('aria-expanded', String(!expanded)); text.classList.toggle('truncate', expanded); text.classList.toggle('whitespace-normal', !expanded); text.classList.toggle('break-words', !expanded);"
    title="<?php echo e($displayText); ?>"
    class="block w-full cursor-pointer text-left focus-visible:rounded focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500">
    <span class="block truncate">
        <?php echo e($displayText); ?>

    </span>
</button><?php /**PATH D:\xampp\htdocs\balantro\resources\views/admin/clients/_expandable_table_text.blade.php ENDPATH**/ ?>