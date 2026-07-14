<?php $__env->startSection('title', 'Staff Dashboard'); ?>
<?php $__env->startSection('subtitle', 'Keep billing, counter operations, and inventory lookup fast and accurate.'); ?>

<?php $__env->startSection('overview'); ?>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['title' => 'My Sales Today','value' => '₹'.e(number_format($todayRevenue, 2)).'','description' => 'Rung up by you today']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'My Sales Today','value' => '₹'.e(number_format($todayRevenue, 2)).'','description' => 'Rung up by you today']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['title' => 'My Invoices Today','value' => ''.e($todayInvoices).'','description' => 'Bills you generated today']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'My Invoices Today','value' => ''.e($todayInvoices).'','description' => 'Bills you generated today']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['title' => 'Role','value' => ''.e(auth()->user()->roles->pluck('name')->first() ?? 'Staff').'','description' => 'Your assigned position']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Role','value' => ''.e(auth()->user()->roles->pluck('name')->first() ?? 'Staff').'','description' => 'Your assigned position']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('dashboard-content'); ?>
    <section class="card card-pad">
        <h2 class="section-title mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', \App\Models\Sale::class)): ?>
                <a href="<?php echo e(route('admin.pos.index')); ?>" class="flex items-center gap-3 rounded-xl border border-outline-variant/40 p-4 transition hover:border-primary hover:bg-primary/5">
                    <span class="icon-tile bg-primary/10 text-primary"><span class="material-symbols-outlined text-[20px]">point_of_sale</span></span>
                    <div>
                        <p class="font-semibold text-on-surface">New Sale (POS)</p>
                        <p class="text-xs text-on-surface-variant">Bill a customer</p>
                    </div>
                </a>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', \App\Models\Sale::class)): ?>
                <a href="<?php echo e(route('admin.sales.index')); ?>" class="flex items-center gap-3 rounded-xl border border-outline-variant/40 p-4 transition hover:border-primary hover:bg-primary/5">
                    <span class="icon-tile bg-primary/10 text-primary"><span class="material-symbols-outlined text-[20px]">receipt_long</span></span>
                    <div>
                        <p class="font-semibold text-on-surface">Sales History</p>
                        <p class="text-xs text-on-surface-variant">View past bills</p>
                    </div>
                </a>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('viewAny', \App\Models\Product::class)): ?>
                <a href="<?php echo e(route('admin.products.index')); ?>" class="flex items-center gap-3 rounded-xl border border-outline-variant/40 p-4 transition hover:border-primary hover:bg-primary/5">
                    <span class="icon-tile bg-primary/10 text-primary"><span class="material-symbols-outlined text-[20px]">inventory_2</span></span>
                    <div>
                        <p class="font-semibold text-on-surface">Inventory</p>
                        <p class="text-xs text-on-surface-variant">Look up medicines &amp; stock</p>
                    </div>
                </a>
            <?php endif; ?>

        </div>

        <?php if (! (auth()->user()->can('create', \App\Models\Sale::class) || auth()->user()->can('viewAny', \App\Models\Product::class))): ?>
            <p class="mt-4 text-sm text-on-surface-variant">Your account doesn't have any operational tools assigned yet. Ask your pharmacy owner to update your position.</p>
        <?php endif; ?>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/staff/dashboard.blade.php ENDPATH**/ ?>