<?php $__env->startSection('title', 'Purchases'); ?>

<?php $__env->startSection('content'); ?>
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Purchases</h1>
            <p class="page-subtitle">Goods received from suppliers. Recording a purchase stocks in new batches.</p>
        </div>
        <a href="<?php echo e(route('admin.purchases.create')); ?>" class="btn btn-primary">
            <span class="material-symbols-outlined text-[18px]">add</span> New Purchase
        </a>
    </div>

    <!-- Filters -->
    <form method="GET" action="<?php echo e(route('admin.purchases.index')); ?>" class="card card-pad">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
            <div class="md:col-span-2">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Invoice number" class="form-input">
            </div>
            <div>
                <label class="form-label">Supplier</label>
                <select name="supplier_id" class="form-select">
                    <option value="">All</option>
                    <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($supplier->id); ?>" <?php if(request('supplier_id') == $supplier->id): echo 'selected'; endif; ?>>
                            <?php echo e($supplier->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button class="btn btn-primary btn-sm">Apply</button>
                <a href="<?php echo e(route('admin.purchases.index')); ?>" class="btn btn-outline btn-sm">Reset</a>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Supplier</th>
                        <th>Supplier Inv #</th>
                        <th>Date</th>
                        <th class="text-right">Amount</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="font-medium"><?php echo e($invoice->invoice_number); ?></td>
                            <td class="text-on-surface-variant"><?php echo e($invoice->supplier?->name ?? '-'); ?></td>
                            <td class="text-on-surface-variant"><?php echo e($invoice->supplier_invoice_number ?? '-'); ?></td>
                            <td class="text-on-surface-variant"><?php echo e($invoice->purchase_date->format('M d, Y')); ?></td>
                            <td class="text-right font-semibold">₹<?php echo e(number_format($invoice->total_amount, 2)); ?></td>
                            <td>
                                <div class="flex items-center justify-end gap-1">
                                    <a class="btn-icon" title="View" href="<?php echo e(route('admin.purchases.show', $invoice)); ?>">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6">
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
        <?php if($invoices->hasPages()): ?>
            <div class="card-footer"><?php echo e($invoices->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/admin/purchases/index.blade.php ENDPATH**/ ?>