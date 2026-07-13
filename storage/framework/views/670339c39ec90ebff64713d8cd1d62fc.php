<?php $__env->startSection('title', 'Coupons'); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-4 flex items-center justify-between">
        <h2 class="section-title">Coupons</h2>
        <a href="<?php echo e(route('superadmin.coupons.create')); ?>" class="btn btn-primary btn-sm">
            <span class="material-symbols-outlined text-[18px]">add</span> New coupon
        </a>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Discount</th>
                        <th>Redemptions</th>
                        <th>Expires</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $coupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coupon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="font-mono-data font-medium text-on-surface"><?php echo e($coupon->code); ?></div>
                                <?php if($coupon->description): ?><div class="text-xs text-on-surface-variant"><?php echo e($coupon->description); ?></div><?php endif; ?>
                            </td>
                            <td><?php echo e($coupon->type === 'percent' ? rtrim(rtrim(number_format($coupon->value, 2), '0'), '.') . '%' : '₹' . number_format($coupon->value, 2)); ?></td>
                            <td class="text-on-surface-variant"><?php echo e($coupon->redeemed_count); ?><?php echo e($coupon->max_redemptions ? ' / ' . $coupon->max_redemptions : ''); ?></td>
                            <td class="text-on-surface-variant"><?php echo e(optional($coupon->expires_at)->format('d M Y') ?? '—'); ?></td>
                            <td>
                                <?php if($coupon->isRedeemable()): ?>
                                    <span class="badge badge-success">Live</span>
                                <?php else: ?>
                                    <span class="badge badge-neutral"><?php echo e($coupon->is_active ? 'Depleted/Expired' : 'Inactive'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="<?php echo e(route('superadmin.coupons.edit', $coupon)); ?>" class="btn btn-xs btn-outline">Edit</a>
                                    <form method="POST" action="<?php echo e(route('superadmin.coupons.toggle', $coupon)); ?>" class="inline">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                        <button class="btn btn-xs bg-tertiary-container/15 text-tertiary hover:bg-tertiary-container/25"><?php echo e($coupon->is_active ? 'Deactivate' : 'Activate'); ?></button>
                                    </form>
                                    <?php if($coupon->redeemed_count === 0): ?>
                                        <form method="POST" action="<?php echo e(route('superadmin.coupons.destroy', $coupon)); ?>" class="inline" onsubmit="return confirm('Delete this coupon?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button class="btn btn-xs bg-error-container text-on-error-container hover:opacity-90">Delete</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6"><div class="empty-state">No coupons yet.</div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($coupons->hasPages()): ?>
            <div class="card-footer"><?php echo e($coupons->links()); ?></div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/superadmin/coupons/index.blade.php ENDPATH**/ ?>