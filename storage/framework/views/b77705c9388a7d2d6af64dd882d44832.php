<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <?php
            $cards = [
                ['Total Pharmacies', $totalPharmacies, $activePharmacies.' active', 'text-primary'],
                ['Active Subscriptions', $activeSubs, $trialSubs.' on trial', 'text-tertiary'],
            ];
            // Revenue figures are billing data — hide from admins without that capability.
            if (auth()->user()->hasAdminCapability(\App\Support\AdminCapability::BILLING)) {
                $cards[] = ['Revenue (This Month)', '₹'.number_format($monthlyRevenue, 2), 'paid invoices', 'text-secondary'];
                $cards[] = ['Total Revenue', '₹'.number_format($totalRevenue, 2), 'all time', 'text-amber-600'];
            }
        ?>
        <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $value, $sub, $color]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card card-pad">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant"><?php echo e($label); ?></p>
                <p class="mt-1 text-2xl font-bold <?php echo e($color); ?>"><?php echo e($value); ?></p>
                <p class="mt-1 text-xs text-on-surface-variant"><?php echo e($sub); ?></p>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
        
        <?php if(auth()->user()->hasAdminCapability(\App\Support\AdminCapability::PHARMACIES)): ?>
        <div class="card overflow-hidden lg:col-span-2">
            <div class="card-header">
                <h2 class="section-title">Recent Pharmacies</h2>
                <a href="<?php echo e(route('superadmin.pharmacies.index')); ?>" class="text-sm font-semibold text-primary hover:underline">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="table-saas">
                    <thead>
                        <tr><th>Pharmacy</th><th>Plan</th><th>Status</th><th>Joined</th></tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $recentPharmacies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pharmacy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <a href="<?php echo e(route('superadmin.pharmacies.show', $pharmacy)); ?>" class="font-medium text-on-surface hover:text-primary"><?php echo e($pharmacy->name); ?></a>
                                    <div class="text-xs text-on-surface-variant"><?php echo e($pharmacy->email); ?></div>
                                </td>
                                <td><?php echo e($pharmacy->currentSubscription?->plan?->name ?? '—'); ?></td>
                                <td><span class="badge <?php echo e($pharmacy->isActive() ? 'badge-success' : 'badge-danger'); ?>"><?php echo e(ucfirst($pharmacy->status)); ?></span></td>
                                <td class="text-on-surface-variant"><?php echo e($pharmacy->created_at->format('d M Y')); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="4"><div class="empty-state">No pharmacies yet.</div></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        
        <?php if(auth()->user()->hasAdminCapability(\App\Support\AdminCapability::BILLING)): ?>
        <div class="card card-pad">
            <h2 class="section-title mb-4">Plan Distribution</h2>
            <div class="space-y-3">
                <?php $__empty_1 = true; $__currentLoopData = $planDistribution; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-on-surface"><?php echo e($row->plan?->name ?? 'Unknown'); ?></span>
                        <span class="font-semibold text-on-surface"><?php echo e($row->total); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-sm text-on-surface-variant">No active subscriptions.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/superadmin/dashboard.blade.php ENDPATH**/ ?>