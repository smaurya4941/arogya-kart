<?php $__env->startSection('title', 'Products'); ?>

<?php $__env->startSection('content'); ?>
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Products</h1>
            <p class="page-subtitle">Manage catalog items and batch inventory.</p>
        </div>
        <a href="<?php echo e(route('admin.products.create')); ?>" class="btn btn-primary">
            <span class="material-symbols-outlined text-[18px]">add</span> Add Product
        </a>
    </div>

    <!-- Filters -->
    <form method="GET" action="<?php echo e(route('admin.products.index')); ?>" class="card card-pad">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-5">
            <div class="md:col-span-2">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Name, SKU, or Barcode" class="form-input">
            </div>
            <div>
                <label class="form-label">SKU</label>
                <input type="text" name="sku" value="<?php echo e(request('sku')); ?>" class="form-input">
            </div>
            <div>
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">All</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($category->id); ?>" <?php if(request('category_id') == $category->id): echo 'selected'; endif; ?>>
                            <?php echo e($category->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="form-label">Drug Type</label>
                <input type="text" name="drug_type" value="<?php echo e(request('drug_type')); ?>" class="form-input">
            </div>
        </div>
        <div class="mt-3 flex gap-2">
            <button class="btn btn-primary btn-sm">Apply</button>
            <a href="<?php echo e(route('admin.products.index')); ?>" class="btn btn-outline btn-sm">Reset</a>
        </div>
    </form>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <!-- Product list -->
        <div class="card col-span-1 overflow-hidden lg:col-span-2">
            <div class="card-header">
                <h2 class="section-title">Product List</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="table-saas">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Batches</th>
                            <th>Stock</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="font-medium"><?php echo e($product->name); ?></td>
                                <td class="text-on-surface-variant"><?php echo e($product->sku); ?></td>
                                <td class="text-on-surface-variant"><?php echo e($product->category?->name ?? '-'); ?></td>
                                <td class="text-on-surface-variant"><?php echo e($product->drug_type ?? '-'); ?></td>
                                <td><?php echo e($product->batches_count ?? 0); ?></td>
                                <td>
                                    <?php $stock = $product->total_stock ?? 0; ?>
                                    <span class="badge <?php echo e($stock > 0 ? 'badge-success' : 'badge-danger'); ?>"><?php echo e($stock); ?></span>
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a class="btn-icon" title="View" href="<?php echo e(route('admin.products.show', $product)); ?>">
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        </a>
                                        <a class="btn-icon" title="Edit" href="<?php echo e(route('admin.products.edit', $product)); ?>">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <span class="material-symbols-outlined text-[32px] opacity-40">inventory_2</span>
                                        No products yet.
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($products->hasPages()): ?>
                <div class="card-footer"><?php echo e($products->links()); ?></div>
            <?php endif; ?>
        </div>

        <!-- Expiring soon -->
        <div class="card col-span-1">
            <div class="card-header">
                <div>
                    <h2 class="section-title">Expiring Soon</h2>
                    <p class="text-xs text-on-surface-variant">Next 30 days</p>
                </div>
            </div>
            <div class="space-y-2 p-3">
                <?php $__empty_1 = true; $__currentLoopData = $expiringBatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="rounded-lg border border-outline-variant/30 bg-surface-container-low/50 p-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="truncate text-sm font-semibold text-on-surface"><?php echo e($batch->product->name ?? 'Unknown Product'); ?></span>
                            <span class="badge badge-warning">Qty <?php echo e($batch->quantity); ?></span>
                        </div>
                        <div class="mt-1 text-xs text-on-surface-variant">
                            Batch <?php echo e($batch->batch_number); ?> · Exp <?php echo e($batch->expiry_date->format('M d, Y')); ?>

                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="empty-state">
                        <span class="material-symbols-outlined text-[32px] opacity-40">event_available</span>
                        No expiring batches.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/admin/products/index.blade.php ENDPATH**/ ?>