<?php $__env->startSection('title', $pharmacy->name); ?>

<?php $__env->startSection('content'); ?>
    <a href="<?php echo e(route('superadmin.pharmacies.index')); ?>" class="text-sm font-medium text-primary hover:underline">&larr; Back to pharmacies</a>

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
        
        <div class="card card-pad">
            <div class="flex items-center justify-between">
                <h2 class="section-title">Profile</h2>
                <?php if($pharmacy->trashed()): ?>
                    <span class="badge badge-danger">Archived</span>
                <?php else: ?>
                    <span class="badge <?php echo e($pharmacy->isActive() ? 'badge-success' : 'badge-danger'); ?>"><?php echo e(ucfirst($pharmacy->status)); ?></span>
                <?php endif; ?>
            </div>
            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-on-surface-variant">Owner</dt><dd><?php echo e($pharmacy->owner_name ?? '—'); ?></dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Email</dt><dd><?php echo e($pharmacy->email ?? '—'); ?></dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Phone</dt><dd><?php echo e($pharmacy->phone ?? '—'); ?></dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">GST</dt><dd><?php echo e($pharmacy->gst ?? '—'); ?></dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Drug License</dt><dd><?php echo e($pharmacy->drug_license_number ?? '—'); ?></dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Joined</dt><dd><?php echo e($pharmacy->created_at->format('d M Y')); ?></dd></div>
            </dl>
            <?php if($pharmacy->trashed()): ?>
                <form method="POST" action="<?php echo e(route('superadmin.pharmacies.restore', $pharmacy->id)); ?>" class="mt-5"
                      onsubmit="return confirm('Restore this pharmacy?')">
                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                    <button class="btn w-full bg-tertiary-container/15 text-tertiary hover:bg-tertiary-container/25">Restore pharmacy</button>
                </form>
            <?php else: ?>
                <a href="<?php echo e(route('superadmin.pharmacies.edit', $pharmacy)); ?>" class="btn btn-primary mt-5 w-full">Edit profile</a>

                <form method="POST" action="<?php echo e(route('superadmin.pharmacies.toggle-status', $pharmacy)); ?>" class="mt-3"
                      onsubmit="return confirm('<?php echo e($pharmacy->isActive() ? 'Suspend' : 'Reactivate'); ?> this pharmacy?')">
                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                    <button class="btn w-full <?php echo e($pharmacy->isActive() ? 'bg-error-container text-on-error-container hover:opacity-90' : 'bg-tertiary-container/15 text-tertiary hover:bg-tertiary-container/25'); ?>">
                        <?php echo e($pharmacy->isActive() ? 'Suspend pharmacy' : 'Reactivate pharmacy'); ?>

                    </button>
                </form>

                <?php if(auth()->user()->hasAdminCapability(\App\Support\AdminCapability::IMPERSONATE)): ?>
                    <form method="POST" action="<?php echo e(route('superadmin.pharmacies.impersonate', $pharmacy)); ?>" class="mt-3"
                          onsubmit="return confirm('Log in as this pharmacy to provide support?')">
                        <?php echo csrf_field(); ?>
                        <button class="btn btn-outline w-full">Impersonate owner</button>
                    </form>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('superadmin.pharmacies.destroy', $pharmacy)); ?>" class="mt-3"
                      onsubmit="return confirm('Archive this pharmacy? Its users lose access until it is restored.')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button class="btn btn-outline w-full text-error">Archive pharmacy</button>
                </form>
            <?php endif; ?>
        </div>

        
        <div class="space-y-4 lg:col-span-2">
            <div class="card card-pad">
                <h2 class="section-title mb-4">Current Subscription</h2>
                <?php if($sub = $pharmacy->currentSubscription): ?>
                    <div class="grid grid-cols-2 gap-4 text-sm sm:grid-cols-4">
                        <div><p class="text-on-surface-variant">Plan</p><p class="font-semibold"><?php echo e($sub->plan?->name ?? '—'); ?></p></div>
                        <div><p class="text-on-surface-variant">Status</p><p class="font-semibold"><?php echo e(ucfirst($sub->status)); ?></p></div>
                        <div><p class="text-on-surface-variant">Cycle</p><p class="font-semibold"><?php echo e(ucfirst($sub->billing_cycle)); ?></p></div>
                        <div><p class="text-on-surface-variant">Ends</p><p class="font-semibold"><?php echo e(optional($sub->currentPeriodEnd())->format('d M Y') ?? '—'); ?></p></div>
                    </div>
                <?php else: ?>
                    <p class="text-sm text-on-surface-variant">No subscription on record.</p>
                <?php endif; ?>
            </div>

            <div class="card overflow-hidden">
                <div class="card-header">
                    <h2 class="section-title">Users (<?php echo e($pharmacy->users->count()); ?>)</h2>
                    <a href="<?php echo e(route('superadmin.users.index', ['pharmacy_id' => $pharmacy->id])); ?>" class="text-sm font-semibold text-primary hover:underline">Manage all</a>
                </div>
                <table class="table-saas">
                    <thead>
                        <tr><th>Name</th><th>Email</th><th>Role</th><th class="text-right">Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $pharmacy->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="font-medium"><?php echo e($user->name); ?></td>
                                <td class="text-on-surface-variant"><?php echo e($user->email); ?></td>
                                <td><?php echo e(ucfirst(str_replace('_',' ', $user->role?->value ?? $user->role))); ?></td>
                                <td class="text-right"><a href="<?php echo e(route('superadmin.users.edit', $user)); ?>" class="btn btn-xs btn-outline">Edit</a></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="card overflow-hidden">
                <div class="card-header"><h2 class="section-title">Recent Invoices</h2></div>
                <table class="table-saas">
                    <thead>
                        <tr><th>Invoice</th><th>Date</th><th>Total</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $pharmacy->invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="font-mono-data"><?php echo e($invoice->invoice_number); ?></td>
                                <td class="text-on-surface-variant"><?php echo e($invoice->created_at->format('d M Y')); ?></td>
                                <td>₹<?php echo e(number_format($invoice->total, 2)); ?></td>
                                <td><?php echo e(ucfirst($invoice->status)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="4"><div class="empty-state">No invoices.</div></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/superadmin/pharmacies/show.blade.php ENDPATH**/ ?>