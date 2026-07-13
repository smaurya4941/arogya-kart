<?php $__env->startSection('title', 'Sale ' . $sale->invoice_number); ?>

<?php $__env->startSection('content'); ?>
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title"><?php echo e($sale->invoice_number); ?></h1>
            <p class="page-subtitle"><?php echo e($sale->sale_date->format('d M Y, h:i A')); ?></p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.sales.invoice', $sale)); ?>" target="_blank" class="btn btn-outline">
                <span class="material-symbols-outlined text-[18px]">print</span> Print Invoice
            </a>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', \App\Models\SaleReturn::class)): ?>
                <?php if($sale->hasReturnableItems()): ?>
                    <a href="<?php echo e(route('admin.returns.create', $sale)); ?>" class="btn bg-amber-500 text-white hover:bg-amber-600">Process Return</a>
                <?php endif; ?>
            <?php endif; ?>
            <a href="<?php echo e(route('admin.sales.create')); ?>" class="btn btn-primary">New Sale</a>
            <a href="<?php echo e(route('admin.sales.index')); ?>" class="btn btn-outline">Back</a>
        </div>
    </div>

    <?php if($sale->returns->isNotEmpty()): ?>
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-5 py-3 text-sm text-amber-800">
            <span class="font-medium">₹<?php echo e(number_format($sale->totalRefunded(), 2)); ?></span> refunded across <?php echo e($sale->returns->count()); ?> return(s).
            <?php $__currentLoopData = $sale->returns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('admin.returns.show', $r)); ?>" class="ml-1 underline"><?php echo e($r->return_number); ?></a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="card card-pad space-y-3">
            <h2 class="section-title">Bill Details</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-on-surface-variant">Customer</dt><dd><?php echo e($sale->customer?->name ?? 'Walk-in'); ?></dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Cashier</dt><dd><?php echo e($sale->cashier?->name ?? '-'); ?></dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Payment</dt><dd class="capitalize"><?php echo e($sale->payment_method); ?></dd></div>
                <div class="flex justify-between">
                    <dt class="text-on-surface-variant">Status</dt>
                    <dd><span class="badge capitalize <?php echo e($sale->paymentStatusBadge()); ?>"><?php echo e($sale->payment_status); ?></span></dd>
                </div>
                <?php if($sale->notes): ?>
                    <div class="pt-2"><dt class="mb-1 text-on-surface-variant">Notes</dt><dd><?php echo e($sale->notes); ?></dd></div>
                <?php endif; ?>
            </dl>
        </div>

        <div class="card overflow-hidden lg:col-span-2">
            <div class="overflow-x-auto">
                <table class="table-saas">
                    <thead>
                        <tr>
                            <th>Medicine</th>
                            <th>Batch</th>
                            <th class="text-right">Price</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Disc %</th>
                            <th class="text-right">GST %</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="font-medium"><?php echo e($item->product?->name ?? '—'); ?></td>
                                <td class="text-on-surface-variant"><?php echo e($item->batch?->batch_number ?? '—'); ?></td>
                                <td class="text-right">₹<?php echo e(number_format((float) $item->unit_price, 2)); ?></td>
                                <td class="text-right"><?php echo e($item->quantity); ?></td>
                                <td class="text-right"><?php echo e(rtrim(rtrim(number_format((float) $item->discount_percentage, 2), '0'), '.')); ?></td>
                                <td class="text-right"><?php echo e(rtrim(rtrim(number_format((float) $item->tax_percentage, 2), '0'), '.')); ?></td>
                                <td class="text-right font-medium">₹<?php echo e(number_format((float) $item->total, 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot>
                        <tr><td colspan="6" class="px-4 py-1.5 text-right text-on-surface-variant">Subtotal</td><td class="px-4 py-1.5 text-right">₹<?php echo e(number_format((float) $sale->subtotal, 2)); ?></td></tr>
                        <tr><td colspan="6" class="px-4 py-1.5 text-right text-on-surface-variant">GST</td><td class="px-4 py-1.5 text-right">₹<?php echo e(number_format((float) $sale->tax_amount, 2)); ?></td></tr>
                        <tr><td colspan="6" class="px-4 py-1.5 text-right text-on-surface-variant">Discount</td><td class="px-4 py-1.5 text-right">− ₹<?php echo e(number_format((float) $sale->discount_amount, 2)); ?></td></tr>
                        <tr class="font-bold"><td colspan="6" class="px-4 py-2 text-right">Grand Total</td><td class="px-4 py-2 text-right">₹<?php echo e(number_format((float) $sale->total_amount, 2)); ?></td></tr>
                        <tr><td colspan="6" class="px-4 py-1.5 text-right text-on-surface-variant">Paid</td><td class="px-4 py-1.5 text-right">₹<?php echo e(number_format((float) $sale->paid_amount, 2)); ?></td></tr>
                        <?php if($sale->due_amount > 0): ?>
                            <tr class="text-error"><td colspan="6" class="px-4 py-1.5 text-right">Balance Due</td><td class="px-4 py-1.5 text-right">₹<?php echo e(number_format((float) $sale->due_amount, 2)); ?></td></tr>
                        <?php endif; ?>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/admin/sales/show.blade.php ENDPATH**/ ?>