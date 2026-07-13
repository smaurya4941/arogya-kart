<?php $__env->startSection('title', 'Create Category'); ?>

<?php $__env->startSection('content'); ?>
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Create Category</h1>
            <p class="page-subtitle">Add a new category to organize your products.</p>
        </div>
        <a href="<?php echo e(route('admin.categories.index')); ?>" class="btn btn-outline">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Back
        </a>
    </div>

    <form method="POST" action="<?php echo e(route('admin.categories.store')); ?>" class="card max-w-2xl">
        <?php echo csrf_field(); ?>
        <div class="card-pad space-y-4">
            <div>
                <label for="name" class="form-label">Category Name</label>
                <input type="text" id="name" name="name" value="<?php echo e(old('name')); ?>" class="form-input <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required autofocus>
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-error"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>
        
        <div class="card-footer bg-surface-container-lowest flex justify-end gap-2">
            <a href="<?php echo e(route('admin.categories.index')); ?>" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Category</button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/admin/categories/create.blade.php ENDPATH**/ ?>