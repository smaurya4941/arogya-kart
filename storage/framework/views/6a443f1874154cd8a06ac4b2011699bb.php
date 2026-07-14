<?php $__env->startSection('title', 'Returns'); ?>

<?php $__env->startSection('content'); ?>
<div class="page mx-auto max-w-6xl">
    <div class="page-header">
        <div>
            <h1 class="page-title">Returns &amp; Refunds</h1>
            <p class="page-subtitle">Credit notes issued against sales.</p>
        </div>
        <form method="GET" class="w-full sm:w-72">
            <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Search return / invoice #" class="form-input">
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Return #</th>
                        <th>Invoice</th>
                        <th>Date</th>
                        <th>Processed by</th>
                        <th class="text-right">Refunded</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $returns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $return): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <a href="<?php echo e(route('admin.returns.show', $return)); ?>" class="font-mono-data font-medium text-primary hover:underline"><?php echo e($return->return_number); ?></a>
                            </td>
                            <td>
                                <a href="<?php echo e(route('admin.sales.show', $return->sale_id)); ?>" class="text-on-surface hover:underline"><?php echo e($return->sale?->invoice_number ?? '—'); ?></a>
                            </td>
                            <td class="text-on-surface-variant"><?php echo e($return->created_at->format('d M Y, h:i A')); ?></td>
                            <td class="text-on-surface-variant"><?php echo e($return->processor?->name ?? '—'); ?></td>
                            <td class="text-right font-semibold">₹<?php echo e(number_format($return->total_amount, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <span class="material-symbols-outlined text-[32px] opacity-40">assignment_return</span>
                                    No returns yet.
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($returns->hasPages()): ?>
            <div class="card-footer"><?php echo e($returns->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/admin/returns/index.blade.php ENDPATH**/ ?>