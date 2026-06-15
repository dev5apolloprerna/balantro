<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['name', 'class' => '', 'width' => null, 'height' => null]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['name', 'class' => '', 'width' => null, 'height' => null]); ?>
<?php foreach (array_filter((['name', 'class' => '', 'width' => null, 'height' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<span <?php echo e($attributes->merge(['class' => $class])); ?>>
    <svg xmlns="http://www.w3.org/2000/svg" 
         <?php if($width): ?> width="<?php echo e($width); ?>" <?php endif; ?>
         <?php if($height): ?> height="<?php echo e($height); ?>" <?php endif; ?>
         viewBox="0 0 24 24" 
         fill="none" 
         stroke="currentColor" 
         stroke-width="2" 
         stroke-linecap="round" 
         stroke-linejoin="round">
        <!-- Icon paths would be defined here based on the name -->
    </svg>
</span><?php /**PATH D:\xampp\htdocs\balantro\resources\views\components\icon.blade.php ENDPATH**/ ?>