<?php $__env->startSection('title', 'Plans'); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-5 flex items-center justify-between">
        <p class="text-sm text-on-surface-variant">Manage the subscription plans offered to pharmacies.</p>
        <a href="<?php echo e(route('superadmin.plans.create')); ?>" class="btn btn-primary btn-sm">
            <span class="material-symbols-outlined text-[16px]">add</span> New Plan
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        <?php $__empty_1 = true; $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="card card-pad">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-base font-bold text-on-surface"><?php echo e($plan->name); ?></h3>
                        <p class="text-xs text-on-surface-variant"><?php echo e($plan->subscriptions_count); ?> subscriptions</p>
                    </div>
                    <span class="badge <?php echo e($plan->is_active ? 'badge-success' : 'badge-neutral'); ?>"><?php echo e($plan->is_active ? 'Active' : 'Inactive'); ?></span>
                </div>
                <p class="mt-3 text-2xl font-extrabold text-on-surface">₹<?php echo e(number_format($plan->price_monthly)); ?><span class="text-sm font-normal text-on-surface-variant">/mo</span></p>
                <p class="text-xs text-on-surface-variant">₹<?php echo e(number_format($plan->price_yearly)); ?>/yr</p>
                <ul class="mt-4 space-y-1 text-sm text-on-surface-variant">
                    <li><?php echo e($plan->max_users); ?> users · <?php echo e($plan->max_branches); ?> branches</li>
                    <li>API access: <?php echo e($plan->api_access ? 'Yes' : 'No'); ?></li>
                </ul>
                <div class="mt-5 flex gap-2">
                    <a href="<?php echo e(route('superadmin.plans.edit', $plan)); ?>" class="btn btn-outline btn-sm flex-1">Edit</a>
                    <form method="POST" action="<?php echo e(route('superadmin.plans.toggle-status', $plan)); ?>" class="flex-1">
                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                        <button class="btn btn-sm w-full <?php echo e($plan->is_active ? 'bg-error-container text-on-error-container hover:opacity-90' : 'bg-tertiary-container/15 text-tertiary hover:bg-tertiary-container/25'); ?>">
                            <?php echo e($plan->is_active ? 'Archive' : 'Activate'); ?>

                        </button>
                    </form>
                </div>
                <?php if($plan->subscriptions_count === 0): ?>
                    <form method="POST" action="<?php echo e(route('superadmin.plans.destroy', $plan)); ?>" class="mt-2" onsubmit="return confirm('Permanently delete this plan?')">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button class="btn btn-xs w-full text-error hover:underline">Delete permanently</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-on-surface-variant">No plans yet.</p>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/superadmin/plans/index.blade.php ENDPATH**/ ?>