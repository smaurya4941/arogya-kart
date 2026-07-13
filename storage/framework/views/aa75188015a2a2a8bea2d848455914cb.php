<?php $__env->startSection('title', 'Suppliers'); ?>

<?php $__env->startSection('content'); ?>
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Suppliers</h1>
            <p class="page-subtitle">Manage the vendors you purchase stock from.</p>
        </div>
        <a href="<?php echo e(route('admin.suppliers.create')); ?>" class="btn btn-primary">
            <span class="material-symbols-outlined text-[18px]">add</span> Add Supplier
        </a>
    </div>

    <form method="GET" action="<?php echo e(route('admin.suppliers.index')); ?>" class="card card-pad">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
            <div class="md:col-span-2">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Name, company, phone or GST" class="form-input">
            </div>
            <div>
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>Active</option>
                    <option value="inactive" <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>>Inactive</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button class="btn btn-primary btn-sm">Apply</button>
                <a href="<?php echo e(route('admin.suppliers.index')); ?>" class="btn btn-outline btn-sm">Reset</a>
            </div>
        </div>
    </form>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Company</th>
                        <th>Phone</th>
                        <th>GST</th>
                        <th>Purchases</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="font-medium"><?php echo e($supplier->name); ?></td>
                            <td class="text-on-surface-variant"><?php echo e($supplier->company_name ?? '-'); ?></td>
                            <td class="text-on-surface-variant"><?php echo e($supplier->phone ?? '-'); ?></td>
                            <td class="text-on-surface-variant"><?php echo e($supplier->gst_number ?? '-'); ?></td>
                            <td><?php echo e($supplier->purchase_invoices_count); ?></td>
                            <td>
                                <?php if($supplier->is_active): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-neutral">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-1">
                                    <a class="btn-icon" title="View" href="<?php echo e(route('admin.suppliers.show', $supplier)); ?>">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                    <a class="btn-icon" title="Edit" href="<?php echo e(route('admin.suppliers.edit', $supplier)); ?>">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <span class="material-symbols-outlined text-[32px] opacity-40">local_shipping</span>
                                    No suppliers yet.
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($suppliers->hasPages()): ?>
            <div class="card-footer"><?php echo e($suppliers->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/admin/suppliers/index.blade.php ENDPATH**/ ?>