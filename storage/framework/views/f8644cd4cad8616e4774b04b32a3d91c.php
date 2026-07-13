<?php $__env->startSection('title', 'Sales Report'); ?>

<?php $__env->startSection('content'); ?>
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Sales Report</h1>
            <p class="page-subtitle"><?php echo e($start->format('d M Y')); ?> &ndash; <?php echo e($end->format('d M Y')); ?></p>
        </div>
    </div>

    <?php echo $__env->make('admin.reports._filters', ['action' => 'admin.reports.sales', 'pdfRoute' => 'admin.reports.sales.pdf'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Invoices</p>
            <p class="mt-1 text-2xl font-bold text-on-surface"><?php echo e(number_format($summary['invoices'])); ?></p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Gross Sales</p>
            <p class="mt-1 text-2xl font-bold text-on-surface">₹<?php echo e(number_format($summary['total'], 2)); ?></p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Collected</p>
            <p class="mt-1 text-2xl font-bold text-tertiary">₹<?php echo e(number_format($summary['paid'], 2)); ?></p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Outstanding Due</p>
            <p class="mt-1 text-2xl font-bold text-error">₹<?php echo e(number_format($summary['due'], 2)); ?></p>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Method</th>
                        <th class="text-right">Subtotal</th>
                        <th class="text-right">Tax</th>
                        <th class="text-right">Total</th>
                        <th class="text-right">Due</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="font-medium">
                                <a class="text-primary hover:underline" href="<?php echo e(route('admin.sales.show', $sale)); ?>"><?php echo e($sale->invoice_number); ?></a>
                            </td>
                            <td class="text-on-surface-variant"><?php echo e($sale->sale_date->format('d M Y')); ?></td>
                            <td class="text-on-surface-variant"><?php echo e($sale->customer->name ?? 'Walk-in'); ?></td>
                            <td class="capitalize text-on-surface-variant"><?php echo e($sale->payment_method); ?></td>
                            <td class="text-right">₹<?php echo e(number_format((float) $sale->subtotal, 2)); ?></td>
                            <td class="text-right">₹<?php echo e(number_format((float) $sale->tax_amount, 2)); ?></td>
                            <td class="text-right font-medium">₹<?php echo e(number_format((float) $sale->total_amount, 2)); ?></td>
                            <td class="text-right <?php echo e((float) $sale->due_amount > 0 ? 'text-error' : 'text-outline'); ?>">₹<?php echo e(number_format((float) $sale->due_amount, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <span class="material-symbols-outlined text-[32px] opacity-40">monitoring</span>
                                    No sales in this period.
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <?php if($sales->count()): ?>
                    <tfoot class="bg-surface-container-low/60 font-semibold">
                        <tr>
                            <td class="px-4 py-3" colspan="4">Totals (this period)</td>
                            <td class="px-4 py-3 text-right">₹<?php echo e(number_format($summary['subtotal'], 2)); ?></td>
                            <td class="px-4 py-3 text-right">₹<?php echo e(number_format($summary['tax'], 2)); ?></td>
                            <td class="px-4 py-3 text-right">₹<?php echo e(number_format($summary['total'], 2)); ?></td>
                            <td class="px-4 py-3 text-right">₹<?php echo e(number_format($summary['due'], 2)); ?></td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
        <?php if($sales->hasPages()): ?>
            <div class="card-footer"><?php echo e($sales->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/admin/reports/sales.blade.php ENDPATH**/ ?>