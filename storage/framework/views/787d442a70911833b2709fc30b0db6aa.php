<?php $__env->startSection('title', 'Subscriptions'); ?>

<?php
    $statusBadge = [
        'active'    => 'badge-success',
        'trial'     => 'badge-success',
        'expired'   => 'badge-neutral',
        'cancelled' => 'badge-danger',
        'suspended' => 'badge-danger',
    ];
?>

<?php $__env->startSection('content'); ?>
    <div class="mb-4 flex items-center justify-between">
        <h2 class="section-title">Subscriptions</h2>
        <a href="<?php echo e(route('superadmin.subscriptions.create')); ?>" class="btn btn-primary btn-sm">
            <span class="material-symbols-outlined text-[18px]">add</span> New subscription
        </a>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <form method="GET" class="flex w-full flex-wrap gap-2">
                <select name="status" class="form-select w-auto">
                    <option value="">All statuses</option>
                    <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($status); ?>" <?php if(request('status') === $status): echo 'selected'; endif; ?>><?php echo e(ucfirst($status)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="plan_id" class="form-select w-auto">
                    <option value="">All plans</option>
                    <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($plan->id); ?>" <?php if((string) request('plan_id') === (string) $plan->id): echo 'selected'; endif; ?>><?php echo e($plan->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button class="btn btn-primary btn-sm">Filter</button>
                <?php if(request()->hasAny(['status', 'plan_id'])): ?>
                    <a href="<?php echo e(route('superadmin.subscriptions.index')); ?>" class="btn btn-outline btn-sm">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Pharmacy</th>
                        <th>Plan</th>
                        <th>Cycle</th>
                        <th>Status</th>
                        <th>Period end</th>
                        <th>Invoices</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $subscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <a href="<?php echo e(route('superadmin.pharmacies.show', $sub->pharmacy_id)); ?>" class="font-medium text-on-surface hover:text-primary"><?php echo e($sub->pharmacy?->name ?? '—'); ?></a>
                            </td>
                            <td><?php echo e($sub->plan?->name ?? '—'); ?></td>
                            <td><?php echo e(ucfirst($sub->billing_cycle)); ?></td>
                            <td><span class="badge <?php echo e($statusBadge[$sub->status] ?? 'badge-neutral'); ?>"><?php echo e(ucfirst($sub->status)); ?></span></td>
                            <td class="text-on-surface-variant"><?php echo e(optional($sub->currentPeriodEnd())->format('d M Y') ?? '—'); ?></td>
                            <td class="text-on-surface-variant"><?php echo e($sub->invoices_count); ?></td>
                            <td class="text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="<?php echo e(route('superadmin.subscriptions.edit', $sub)); ?>" class="btn btn-xs btn-outline">Manage</a>
                                    <?php if($sub->status !== 'cancelled'): ?>
                                        <form method="POST" action="<?php echo e(route('superadmin.subscriptions.cancel', $sub)); ?>" class="inline"
                                              onsubmit="return confirm('Cancel this subscription? The pharmacy will lose access.')">
                                            <?php echo csrf_field(); ?>
                                            <button class="btn btn-xs bg-error-container text-on-error-container hover:opacity-90">Cancel</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="7"><div class="empty-state">No subscriptions found.</div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($subscriptions->hasPages()): ?>
            <div class="card-footer"><?php echo e($subscriptions->links()); ?></div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/superadmin/subscriptions/index.blade.php ENDPATH**/ ?>