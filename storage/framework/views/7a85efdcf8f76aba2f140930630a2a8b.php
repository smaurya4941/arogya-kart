<?php $__env->startSection('title', 'Categories'); ?>

<?php $__env->startSection('content'); ?>
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Categories</h1>
            <p class="page-subtitle">Manage product categories to organize your inventory.</p>
        </div>
        <a href="<?php echo e(route('admin.categories.create')); ?>" class="btn btn-primary">
            <span class="material-symbols-outlined text-[18px]">add</span> Add Category
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success mb-4 text-green-600 font-semibold"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger mb-4 text-error font-semibold"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="card overflow-hidden">
        <div class="card-header">
            <h2 class="section-title">Category List</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Products Count</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="font-medium"><?php echo e($category->name); ?></td>
                            <td class="text-on-surface-variant"><?php echo e($category->slug); ?></td>
                            <td>
                                <span class="badge badge-neutral"><?php echo e($category->products_count); ?></span>
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-1">
                                    <a class="btn-icon" title="Edit" href="<?php echo e(route('admin.categories.edit', $category)); ?>">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form action="<?php echo e(route('admin.categories.destroy', $category)); ?>" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn-icon text-error hover:bg-error/10 hover:text-error" title="Delete">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <span class="material-symbols-outlined text-[32px] opacity-40">category</span>
                                    No categories found.
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($categories->hasPages()): ?>
            <div class="card-footer"><?php echo e($categories->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/admin/categories/index.blade.php ENDPATH**/ ?>