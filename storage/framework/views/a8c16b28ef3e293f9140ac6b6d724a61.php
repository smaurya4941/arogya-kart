<?php $__env->startSection('title', 'Add Team Member'); ?>

<?php $__env->startSection('content'); ?>
<div class="page mx-auto max-w-3xl">
    <div class="page-header">
        <h1 class="page-title">Add Team Member</h1>
    </div>
    <div class="card card-pad">
        <form method="POST" action="<?php echo e(route('admin.team.store')); ?>">
            <?php echo csrf_field(); ?>
            <?php echo $__env->make('admin.team._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/admin/team/create.blade.php ENDPATH**/ ?>