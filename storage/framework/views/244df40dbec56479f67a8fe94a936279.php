<?php $__env->startSection('doc-title', 'GST Report'); ?>

<?php $__env->startSection('body'); ?>
<table class="summary">
    <tr>
        <td><div class="label">Output GST (Sales)</div><div class="value">Rs. <?php echo e(number_format($gst['output_tax'], 2)); ?></div></td>
        <td><div class="label">Input GST (Purchases)</div><div class="value">Rs. <?php echo e(number_format($gst['input_tax'], 2)); ?></div></td>
        <td><div class="label">Net GST Payable</div><div class="value">Rs. <?php echo e(number_format($gst['net_payable'], 2)); ?></div></td>
    </tr>
</table>

<h3 style="margin:14px 0 0; font-size:12px;">Output Tax &mdash; Sales</h3>
<table>
    <thead>
        <tr><th>GST Slab</th><th class="right">Taxable Value</th><th class="right">Tax</th></tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $gst['output_slabs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e(number_format((float) $slab->rate, 2)); ?>%</td>
                <td class="right"><?php echo e(number_format((float) $slab->taxable_total - (float) $slab->tax_amount, 2)); ?></td>
                <td class="right"><?php echo e(number_format((float) $slab->tax_amount, 2)); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="3">No taxable sales in this period.</td></tr>
        <?php endif; ?>
    </tbody>
    <?php if($gst['output_slabs']->count()): ?>
        <tfoot><tr><td colspan="2">Total Output GST</td><td class="right"><?php echo e(number_format($gst['output_tax'], 2)); ?></td></tr></tfoot>
    <?php endif; ?>
</table>

<h3 style="margin:14px 0 0; font-size:12px;">Input Tax &mdash; Purchases</h3>
<table>
    <thead>
        <tr><th>GST Slab</th><th class="right">Taxable Value</th><th class="right">Tax</th></tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $gst['input_slabs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e(number_format((float) $slab->rate, 2)); ?>%</td>
                <td class="right"><?php echo e(number_format((float) $slab->taxable_total - (float) $slab->tax_amount, 2)); ?></td>
                <td class="right"><?php echo e(number_format((float) $slab->tax_amount, 2)); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="3">No taxable purchases in this period.</td></tr>
        <?php endif; ?>
    </tbody>
    <?php if($gst['input_slabs']->count()): ?>
        <tfoot><tr><td colspan="2">Total Input GST</td><td class="right"><?php echo e(number_format($gst['input_tax'], 2)); ?></td></tr></tfoot>
    <?php endif; ?>
</table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.reports.pdf._layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/admin/reports/pdf/gst.blade.php ENDPATH**/ ?>