<?php $__env->startSection('title', 'Pharmacies'); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-lg font-semibold text-on-surface">Pharmacies</h1>
        <a href="<?php echo e(route('superadmin.pharmacies.create')); ?>" class="btn btn-primary btn-sm">Onboard pharmacy</a>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <form method="GET" class="flex w-full flex-wrap gap-2">
                <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Search name, email, owner…" class="form-input min-w-[200px] flex-1">
                <select name="status" class="form-select w-auto">
                    <option value="">All statuses</option>
                    <option value="active" <?php if(request('status')==='active'): echo 'selected'; endif; ?>>Active</option>
                    <option value="suspended" <?php if(request('status')==='suspended'): echo 'selected'; endif; ?>>Suspended</option>
                </select>
                <select name="trashed" class="form-select w-auto">
                    <option value="">Active tenants</option>
                    <option value="with" <?php if(request('trashed')==='with'): echo 'selected'; endif; ?>>Include archived</option>
                    <option value="only" <?php if(request('trashed')==='only'): echo 'selected'; endif; ?>>Archived only</option>
                </select>
                <button class="btn btn-primary btn-sm">Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Pharmacy</th>
                        <th>Owner</th>
                        <th>Plan</th>
                        <th>Users</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $pharmacies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pharmacy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="<?php echo e($pharmacy->trashed() ? 'opacity-60' : ''); ?>">
                            <td>
                                <a href="<?php echo e(route('superadmin.pharmacies.show', $pharmacy)); ?>" class="font-medium text-on-surface hover:text-primary"><?php echo e($pharmacy->name); ?></a>
                                <div class="text-xs text-on-surface-variant"><?php echo e($pharmacy->email); ?></div>
                            </td>
                            <td class="text-on-surface-variant"><?php echo e($pharmacy->owner_name); ?></td>
                            <td><?php echo e($pharmacy->currentSubscription?->plan?->name ?? '—'); ?></td>
                            <td class="text-on-surface-variant"><?php echo e($pharmacy->users_count); ?></td>
                            <td>
                                <?php if($pharmacy->trashed()): ?>
                                    <span class="badge badge-danger">Archived</span>
                                <?php else: ?>
                                    <span class="badge <?php echo e($pharmacy->isActive() ? 'badge-success' : 'badge-danger'); ?>"><?php echo e(ucfirst($pharmacy->status)); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <?php if($pharmacy->trashed()): ?>
                                        <form method="POST" action="<?php echo e(route('superadmin.pharmacies.restore', $pharmacy->id)); ?>" class="inline"
                                              onsubmit="return confirm('Restore this pharmacy?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <button class="btn btn-xs bg-tertiary-container/15 text-tertiary hover:bg-tertiary-container/25">Restore</button>
                                        </form>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('superadmin.pharmacies.edit', $pharmacy)); ?>" class="btn btn-xs btn-outline">Edit</a>
                                        <form method="POST" action="<?php echo e(route('superadmin.pharmacies.toggle-status', $pharmacy)); ?>" class="inline"
                                              onsubmit="return confirm('<?php echo e($pharmacy->isActive() ? 'Suspend' : 'Reactivate'); ?> this pharmacy?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <button class="btn btn-xs <?php echo e($pharmacy->isActive() ? 'bg-error-container text-on-error-container hover:opacity-90' : 'bg-tertiary-container/15 text-tertiary hover:bg-tertiary-container/25'); ?>">
                                                <?php echo e($pharmacy->isActive() ? 'Suspend' : 'Reactivate'); ?>

                                            </button>
                                        </form>
                                        <form method="POST" action="<?php echo e(route('superadmin.pharmacies.destroy', $pharmacy)); ?>" class="inline"
                                              onsubmit="return confirm('Archive this pharmacy? Its users lose access until it is restored.')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button class="btn btn-xs btn-outline">Archive</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6"><div class="empty-state">No pharmacies found.</div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($pharmacies->hasPages()): ?>
            <div class="card-footer"><?php echo e($pharmacies->links()); ?></div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/superadmin/pharmacies/index.blade.php ENDPATH**/ ?>