<?php $__env->startSection('title', 'Supplier · ' . $supplier->name); ?>

<?php $__env->startSection('content'); ?>
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title"><?php echo e($supplier->name); ?></h1>
            <p class="page-subtitle"><?php echo e($supplier->company_name ?? 'Supplier details'); ?></p>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo e(route('admin.suppliers.edit', $supplier)); ?>" class="btn btn-primary">Edit</a>
            <a href="<?php echo e(route('admin.suppliers.index')); ?>" class="btn btn-outline">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="card card-pad space-y-3">
            <h2 class="section-title border-b border-outline-variant/30 pb-2">Details</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-on-surface-variant">Contact Person</dt><dd><?php echo e($supplier->contact_person ?? '-'); ?></dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Phone</dt><dd><?php echo e($supplier->phone ?? '-'); ?></dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Email</dt><dd><?php echo e($supplier->email ?? '-'); ?></dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">GST</dt><dd><?php echo e($supplier->gst_number ?? '-'); ?></dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">City / State</dt><dd><?php echo e(trim(($supplier->city ?? '') . ' ' . ($supplier->state ?? '')) ?: '-'); ?></dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Status</dt><dd><?php echo $supplier->is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-neutral">Inactive</span>'; ?></dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Outstanding</dt><dd class="font-semibold">₹<?php echo e(number_format($supplier->balance, 2)); ?></dd></div>
            </dl>
            <?php if($supplier->address): ?>
                <div class="pt-2 text-sm">
                    <p class="text-on-surface-variant">Address</p>
                    <p><?php echo e($supplier->address); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <div class="card overflow-hidden lg:col-span-2">
            <div class="card-header">
                <h2 class="section-title">Recent Purchases (<?php echo e($supplier->purchase_invoices_count); ?>)</h2>
                <a href="<?php echo e(route('admin.purchases.create', ['supplier_id' => $supplier->id])); ?>" class="btn btn-primary btn-sm">
                    <span class="material-symbols-outlined text-[16px]">add</span> New Purchase
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="table-saas">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th class="text-right">Amount</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $recentPurchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="font-medium"><?php echo e($purchase->invoice_number); ?></td>
                                <td class="text-on-surface-variant"><?php echo e($purchase->purchase_date->format('M d, Y')); ?></td>
                                <td class="text-right font-semibold">₹<?php echo e(number_format($purchase->total_amount, 2)); ?></td>
                                <td class="text-right">
                                    <a class="btn-icon ml-auto" title="View" href="<?php echo e(route('admin.purchases.show', $purchase)); ?>">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <span class="material-symbols-outlined text-[32px] opacity-40">shopping_cart_checkout</span>
                                        No purchases recorded yet.
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <form method="POST" action="<?php echo e(route('admin.suppliers.destroy', $supplier)); ?>"
          onsubmit="return confirm('Delete this supplier?');">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
        <button class="text-sm font-medium text-error hover:underline">Delete supplier</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/admin/suppliers/show.blade.php ENDPATH**/ ?>