<?php $__env->startSection('title', 'Edit Customer'); ?>

<?php $__env->startSection('content'); ?>
<div class="page mx-auto max-w-3xl">
    <div class="page-header">
        <h1 class="page-title">Edit Customer</h1>
    </div>

    <form method="POST" action="<?php echo e(route('admin.customers.update', $customer)); ?>" class="card card-pad space-y-4">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <?php echo $__env->make('admin.customers._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="flex gap-2 pt-2">
            <button class="btn btn-primary">Update</button>
            <a href="<?php echo e(route('admin.customers.show', $customer)); ?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/admin/customers/edit.blade.php ENDPATH**/ ?>