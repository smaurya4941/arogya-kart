<?php $__env->startSection('title', 'Create Product'); ?>

<?php $__env->startSection('content'); ?>
<div class="page mx-auto max-w-3xl">
    <div class="page-header">
        <h1 class="page-title">Create Product</h1>
    </div>

    <?php if($errors->any()): ?>
        <div class="rounded-lg border border-error/30 bg-error-container/40 p-3 text-sm text-on-error-container">
            <div class="mb-1 font-semibold">Please fix the following:</div>
            <ul class="list-disc space-y-1 pl-5">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('admin.products.store')); ?>" enctype="multipart/form-data" class="card card-pad space-y-4">
        <?php echo csrf_field(); ?>

        <div>
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select">
                <option value="">Select Category</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($category->id); ?>" <?php if(old('category_id') == $category->id): echo 'selected'; endif; ?>><?php echo e($category->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="form-label">Name</label>
            <input type="text" name="name" value="<?php echo e(old('name')); ?>" class="form-input" required>
        </div>
        <div>
            <label class="form-label">SKU</label>
            <input type="text" name="sku" value="<?php echo e(old('sku')); ?>" class="form-input" required>
        </div>
        <div>
            <label class="form-label">Barcode</label>
            <input type="text" name="barcode" value="<?php echo e(old('barcode')); ?>" class="form-input">
        </div>
        <div>
            <label class="form-label">Description</label>
            <textarea name="description" rows="4" class="form-textarea"><?php echo e(old('description')); ?></textarea>
        </div>
        <div>
            <label class="form-label">Drug Type</label>
            <input type="text" name="drug_type" value="<?php echo e(old('drug_type')); ?>" class="form-input">
        </div>
        <div>
            <label class="form-label">Image</label>
            <input type="file" name="image" class="form-input h-auto py-1.5">
        </div>

        <div class="flex gap-2 pt-2">
            <button class="btn btn-primary">Save</button>
            <a href="<?php echo e(route('admin.products.index')); ?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/admin/products/create.blade.php ENDPATH**/ ?>