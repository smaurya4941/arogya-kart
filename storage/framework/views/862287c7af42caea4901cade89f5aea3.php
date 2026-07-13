<?php $__env->startSection('title', 'Edit User'); ?>

<?php $__env->startSection('content'); ?>
    <a href="<?php echo e(route('superadmin.users.index')); ?>" class="text-sm font-medium text-primary hover:underline">&larr; Back to users</a>

    <div class="card card-pad mt-4 max-w-2xl">
        <form method="POST" action="<?php echo e(route('superadmin.users.update', $user)); ?>">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <?php echo $__env->make('superadmin.users._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/superadmin/users/edit.blade.php ENDPATH**/ ?>