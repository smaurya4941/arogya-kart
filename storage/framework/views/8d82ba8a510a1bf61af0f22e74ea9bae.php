<?php $__env->startSection('title', 'Profit & Loss'); ?>

<?php $__env->startSection('content'); ?>
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Profit &amp; Loss</h1>
            <p class="page-subtitle"><?php echo e($start->format('d M Y')); ?> &ndash; <?php echo e($end->format('d M Y')); ?></p>
        </div>
    </div>

    <?php echo $__env->make('admin.reports._filters', ['action' => 'admin.reports.profit'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Net Revenue</p>
            <p class="mt-1 text-2xl font-bold text-on-surface">₹<?php echo e(number_format($pnl['revenue'], 2)); ?></p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Gross Profit</p>
            <p class="mt-1 text-2xl font-bold text-tertiary">₹<?php echo e(number_format($pnl['gross_profit'], 2)); ?></p>
            <p class="mt-1 text-xs text-on-surface-variant">Margin <?php echo e(number_format($pnl['gross_margin'], 1)); ?>%</p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Net Profit</p>
            <p class="mt-1 text-2xl font-bold <?php echo e($pnl['net_profit'] >= 0 ? 'text-tertiary' : 'text-error'); ?>">₹<?php echo e(number_format($pnl['net_profit'], 2)); ?></p>
            <p class="mt-1 text-xs text-on-surface-variant">Margin <?php echo e(number_format($pnl['net_margin'], 1)); ?>%</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="card overflow-hidden">
            <div class="card-header"><h2 class="section-title">Statement</h2></div>
            <dl class="divide-y divide-outline-variant/20 text-sm">
                <div class="flex justify-between px-4 py-3"><dt class="text-on-surface-variant">Gross Sales</dt><dd>₹<?php echo e(number_format($pnl['gross_sales'], 2)); ?></dd></div>
                <div class="flex justify-between px-4 py-3"><dt class="text-on-surface-variant">Less: Tax Collected (pass-through)</dt><dd>&minus;₹<?php echo e(number_format($pnl['tax_collected'], 2)); ?></dd></div>
                <div class="flex justify-between px-4 py-3 font-medium"><dt>Net Revenue</dt><dd>₹<?php echo e(number_format($pnl['revenue'], 2)); ?></dd></div>
                <div class="flex justify-between px-4 py-3"><dt class="text-on-surface-variant">Less: Cost of Goods Sold</dt><dd>&minus;₹<?php echo e(number_format($pnl['cogs'], 2)); ?></dd></div>
                <div class="flex justify-between px-4 py-3 font-medium"><dt>Gross Profit</dt><dd class="text-tertiary">₹<?php echo e(number_format($pnl['gross_profit'], 2)); ?></dd></div>
                <div class="flex justify-between px-4 py-3"><dt class="text-on-surface-variant">Less: Operating Expenses</dt><dd>&minus;₹<?php echo e(number_format($pnl['expenses_total'], 2)); ?></dd></div>
                <div class="flex justify-between bg-surface-container-low/60 px-4 py-3 text-base font-bold">
                    <dt>Net Profit</dt>
                    <dd class="<?php echo e($pnl['net_profit'] >= 0 ? 'text-tertiary' : 'text-error'); ?>">₹<?php echo e(number_format($pnl['net_profit'], 2)); ?></dd>
                </div>
            </dl>
        </div>

        <div class="card overflow-hidden">
            <div class="card-header"><h2 class="section-title">Expenses by Category</h2></div>
            <table class="table-saas">
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $pnl['expenses_by_category']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($row->category); ?></td>
                            <td class="text-right">₹<?php echo e(number_format((float) $row->amount, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="2" class="text-on-surface-variant">No expenses recorded in this period.</td></tr>
                    <?php endif; ?>
                </tbody>
                <?php if($pnl['expenses_by_category']->count()): ?>
                    <tfoot class="bg-surface-container-low/60 font-semibold">
                        <tr><td class="px-4 py-3">Total</td><td class="px-4 py-3 text-right">₹<?php echo e(number_format($pnl['expenses_total'], 2)); ?></td></tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/admin/reports/profit.blade.php ENDPATH**/ ?>