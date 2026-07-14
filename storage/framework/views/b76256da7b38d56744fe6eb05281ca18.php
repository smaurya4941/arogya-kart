<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title',
    'value',
    'description' => null,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'title',
    'value',
    'description' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div <?php echo e($attributes->class('card card-pad')); ?>>
    <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant"><?php echo e($title); ?></p>
    <p class="mt-1 text-2xl font-bold tracking-tight text-on-surface"><?php echo e($value); ?></p>

    <?php if($description): ?>
        <p class="mt-1 text-xs text-on-surface-variant"><?php echo e($description); ?></p>
    <?php endif; ?>
</div>
<?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/components/card.blade.php ENDPATH**/ ?>