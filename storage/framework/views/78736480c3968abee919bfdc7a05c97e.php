<?php $__env->startSection('title', 'GST Report'); ?>

<?php $__env->startSection('content'); ?>
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">GST Report</h1>
            <p class="page-subtitle"><?php echo e($start->format('d M Y')); ?> &ndash; <?php echo e($end->format('d M Y')); ?></p>
        </div>
    </div>

    <?php echo $__env->make('admin.reports._filters', ['action' => 'admin.reports.gst', 'pdfRoute' => 'admin.reports.gst.pdf'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Output GST (on sales)</p>
            <p class="mt-1 text-2xl font-bold text-on-surface">₹<?php echo e(number_format($gst['output_tax'], 2)); ?></p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Input GST (on purchases)</p>
            <p class="mt-1 text-2xl font-bold text-on-surface">₹<?php echo e(number_format($gst['input_tax'], 2)); ?></p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Net GST Payable</p>
            <p class="mt-1 text-2xl font-bold <?php echo e($gst['net_payable'] >= 0 ? 'text-error' : 'text-tertiary'); ?>">₹<?php echo e(number_format($gst['net_payable'], 2)); ?></p>
            <?php if($gst['net_payable'] < 0): ?>
                <p class="mt-1 text-xs text-on-surface-variant">Input credit carried forward</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="card overflow-hidden">
            <div class="card-header"><h2 class="section-title">Output Tax &mdash; Sales</h2></div>
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>GST Slab</th>
                        <th class="text-right">Taxable Value</th>
                        <th class="text-right">Tax</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $gst['output_slabs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e(number_format((float) $slab->rate, 2)); ?>%</td>
                            <td class="text-right">₹<?php echo e(number_format((float) $slab->taxable_total - (float) $slab->tax_amount, 2)); ?></td>
                            <td class="text-right">₹<?php echo e(number_format((float) $slab->tax_amount, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="3" class="text-on-surface-variant">No taxable sales in this period.</td></tr>
                    <?php endif; ?>
                </tbody>
                <?php if($gst['output_slabs']->count()): ?>
                    <tfoot class="bg-surface-container-low/60 font-semibold">
                        <tr><td class="px-4 py-3" colspan="2">Total Output GST</td><td class="px-4 py-3 text-right">₹<?php echo e(number_format($gst['output_tax'], 2)); ?></td></tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>

        <div class="card overflow-hidden">
            <div class="card-header"><h2 class="section-title">Input Tax &mdash; Purchases</h2></div>
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>GST Slab</th>
                        <th class="text-right">Taxable Value</th>
                        <th class="text-right">Tax</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $gst['input_slabs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e(number_format((float) $slab->rate, 2)); ?>%</td>
                            <td class="text-right">₹<?php echo e(number_format((float) $slab->taxable_total - (float) $slab->tax_amount, 2)); ?></td>
                            <td class="text-right">₹<?php echo e(number_format((float) $slab->tax_amount, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="3" class="text-on-surface-variant">No taxable purchases in this period.</td></tr>
                    <?php endif; ?>
                </tbody>
                <?php if($gst['input_slabs']->count()): ?>
                    <tfoot class="bg-surface-container-low/60 font-semibold">
                        <tr><td class="px-4 py-3" colspan="2">Total Input GST</td><td class="px-4 py-3 text-right">₹<?php echo e(number_format($gst['input_tax'], 2)); ?></td></tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/admin/reports/gst.blade.php ENDPATH**/ ?>