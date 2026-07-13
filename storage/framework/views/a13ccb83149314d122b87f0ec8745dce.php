<?php $__env->startSection('title', 'Product Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title"><?php echo e($product->name); ?></h1>
            <p class="page-subtitle">SKU: <?php echo e($product->sku); ?></p>
            <div class="mt-1 flex flex-wrap gap-x-4 text-xs text-on-surface-variant">
                <span>Category: <?php echo e($product->category?->name ?? '-'); ?></span>
                <span>Barcode: <?php echo e($product->barcode ?? '-'); ?></span>
                <span>Drug Type: <?php echo e($product->drug_type ?? '-'); ?></span>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo e(route('admin.products.edit', $product)); ?>" class="btn btn-outline">Edit</a>
            <form method="POST" action="<?php echo e(route('admin.products.destroy', $product)); ?>" onsubmit="return confirm('Delete this product and its batches?');">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>

    <?php if($product->image_path): ?>
        <div class="card card-pad">
            <h2 class="section-title mb-3">Product Image</h2>
            <img src="<?php echo e(asset('storage/'.$product->image_path)); ?>" alt="<?php echo e($product->name); ?>" class="max-w-xs rounded-lg">
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="card card-pad">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Total Stock</div>
            <div class="mt-1 text-2xl font-bold text-on-surface"><?php echo e($product->total_stock); ?></div>
        </div>
        <div class="card card-pad">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Batches</div>
            <div class="mt-1 text-2xl font-bold text-on-surface"><?php echo e($product->batches->count()); ?></div>
        </div>
        <div class="card card-pad">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Expiring Soon</div>
            <div class="mt-1 text-2xl font-bold text-on-surface"><?php echo e($expiringBatches->count()); ?></div>
        </div>
    </div>

    <div class="card card-pad">
        <h2 class="section-title mb-3">Issue Stock (FEFO)</h2>
        <form method="POST" action="<?php echo e(route('admin.products.issue-stock', $product)); ?>" class="flex flex-col gap-2 sm:flex-row">
            <?php echo csrf_field(); ?>
            <input type="number" name="quantity" min="1" placeholder="Quantity" class="form-input w-full sm:w-48" required>
            <button class="btn btn-primary">Issue Stock</button>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <h2 class="section-title">Batches</h2>
            <a href="<?php echo e(route('admin.products.batches.create', $product)); ?>" class="btn btn-primary btn-sm">
                <span class="material-symbols-outlined text-[16px]">add</span> Add Batch
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Batch</th>
                        <th>Expiry</th>
                        <th>Purchase</th>
                        <th>MRP</th>
                        <th>Qty</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $product->batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="font-medium"><?php echo e($batch->batch_number); ?></td>
                            <td><?php echo e($batch->expiry_date->format('M d, Y')); ?></td>
                            <td><?php echo e(number_format($batch->purchase_price, 2)); ?></td>
                            <td><?php echo e(number_format($batch->mrp, 2)); ?></td>
                            <td><?php echo e($batch->quantity); ?></td>
                            <td>
                                <div class="flex items-center justify-end gap-1">
                                    <a class="btn-icon" title="Edit" href="<?php echo e(route('admin.batches.edit', $batch)); ?>">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form method="POST" action="<?php echo e(route('admin.batches.destroy', $batch)); ?>" class="inline" onsubmit="return confirm('Delete this batch?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button class="btn-icon hover:text-error" title="Delete">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <span class="material-symbols-outlined text-[32px] opacity-40">layers</span>
                                    No batches yet.
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card card-pad">
        <h2 class="section-title mb-3">Expiring Batches</h2>
        <div class="space-y-2">
            <?php $__empty_1 = true; $__currentLoopData = $expiringBatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="rounded-lg border border-outline-variant/30 bg-surface-container-low/50 p-3">
                    <div class="font-medium text-on-surface">Batch <?php echo e($batch->batch_number); ?></div>
                    <div class="text-xs text-on-surface-variant">Expiry: <?php echo e($batch->expiry_date->format('M d, Y')); ?> · Qty: <?php echo e($batch->quantity); ?></div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-on-surface-variant">No expiring batches.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/admin/products/show.blade.php ENDPATH**/ ?>