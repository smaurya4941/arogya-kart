<?php $__env->startSection('title', 'Purchase · ' . $purchase->invoice_number); ?>

<?php $__env->startSection('content'); ?>
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title"><?php echo e($purchase->invoice_number); ?></h1>
            <p class="page-subtitle">
                <?php echo e($purchase->supplier?->name ?? 'Unknown supplier'); ?> ·
                <?php echo e($purchase->purchase_date->format('M d, Y')); ?>

            </p>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo e(route('admin.purchases.create')); ?>" class="btn btn-primary">
                <span class="material-symbols-outlined text-[18px]">add</span> New Purchase
            </a>
            <a href="<?php echo e(route('admin.purchases.index')); ?>" class="btn btn-outline">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Supplier Invoice #</p>
            <p class="mt-1 font-semibold text-on-surface"><?php echo e($purchase->supplier_invoice_number ?? '-'); ?></p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Payment Terms</p>
            <p class="mt-1 font-semibold text-on-surface"><?php echo e($purchase->payment_terms ?? '-'); ?></p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Items</p>
            <p class="mt-1 font-semibold text-on-surface"><?php echo e($purchase->items->count()); ?></p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Total Amount</p>
            <p class="mt-1 font-semibold text-primary">₹<?php echo e(number_format($purchase->total_amount, 2)); ?></p>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <h2 class="section-title">Line Items</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Batch #</th>
                        <th>Expiry</th>
                        <th>Qty</th>
                        <th>Buy Price</th>
                        <th>MRP</th>
                        <th>Sell Price</th>
                        <th>GST %</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $purchase->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="font-medium"><?php echo e($item->product?->name ?? '-'); ?></td>
                            <td class="text-on-surface-variant"><?php echo e($item->batch?->batch_number ?? '-'); ?></td>
                            <td class="text-on-surface-variant"><?php echo e($item->batch?->expiry_date?->format('M d, Y') ?? '-'); ?></td>
                            <td><?php echo e($item->quantity); ?></td>
                            <td>₹<?php echo e(number_format($item->purchase_price, 2)); ?></td>
                            <td>₹<?php echo e(number_format($item->mrp, 2)); ?></td>
                            <td>₹<?php echo e(number_format($item->selling_price, 2)); ?></td>
                            <td><?php echo e(rtrim(rtrim(number_format($item->gst_percentage, 2), '0'), '.')); ?>%</td>
                            <td class="text-right font-medium">₹<?php echo e(number_format($item->total, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr class="bg-surface-container-low/60">
                        <td colspan="8" class="px-4 py-3 text-right font-semibold">Grand Total</td>
                        <td class="px-4 py-3 text-right font-bold text-on-surface">₹<?php echo e(number_format($purchase->total_amount, 2)); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <?php if($purchase->notes): ?>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Notes</p>
            <p class="mt-1 text-sm text-on-surface"><?php echo e($purchase->notes); ?></p>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/admin/purchases/show.blade.php ENDPATH**/ ?>