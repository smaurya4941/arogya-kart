<?php $__env->startSection('title', 'Team'); ?>

<?php $__env->startSection('content'); ?>
<div class="page mx-auto max-w-6xl">
    <div class="page-header">
        <div>
            <h1 class="page-title">Team</h1>
            <p class="page-subtitle">
                Manage the people who work in your pharmacy.
                <?php if(!is_null($seatLimit)): ?>
                    <span class="ml-1 font-medium text-on-surface"><?php echo e($seatsUsed); ?> / <?php echo e($seatLimit); ?> seats used.</span>
                <?php endif; ?>
            </p>
        </div>
        <?php if($canAdd): ?>
            <a href="<?php echo e(route('admin.team.create')); ?>" class="btn btn-primary">
                <span class="material-symbols-outlined text-[18px]">person_add</span> Add Member
            </a>
        <?php else: ?>
            <a href="<?php echo e(route('admin.subscription.index')); ?>" class="btn btn-primary bg-amber-500 hover:bg-amber-600" title="Plan seat limit reached">
                <span class="material-symbols-outlined text-[18px]">upgrade</span> Upgrade to add more
            </a>
        <?php endif; ?>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $isOwner = $member->isAdmin(); $isSelf = $member->id === auth()->id(); ?>
                        <tr>
                            <td>
                                <div class="font-medium text-on-surface"><?php echo e($member->name); ?> <?php if($isSelf): ?><span class="text-xs text-on-surface-variant">(you)</span><?php endif; ?></div>
                                <div class="text-xs text-on-surface-variant"><?php echo e($member->email); ?></div>
                            </td>
                            <td>
                                <?php if($isOwner): ?>
                                    <span class="badge badge-info">Owner</span>
                                <?php else: ?>
                                    <?php echo e($member->roles->pluck('name')->join(', ') ?: '—'); ?>

                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge <?php echo e($member->status === 'active' ? 'badge-success' : 'badge-neutral'); ?>"><?php echo e(ucfirst($member->status)); ?></span>
                            </td>
                            <td>
                                <?php if($isOwner || $isSelf): ?>
                                    <span class="float-right text-xs text-outline-variant">—</span>
                                <?php else: ?>
                                    <div class="flex justify-end gap-2">
                                        <a href="<?php echo e(route('admin.team.edit', $member)); ?>" class="btn btn-outline btn-xs">Edit</a>
                                        <form method="POST" action="<?php echo e(route('admin.team.toggle-status', $member)); ?>">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <button class="btn btn-xs <?php echo e($member->status === 'active' ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-tertiary-container/15 text-tertiary hover:bg-tertiary-container/25'); ?>">
                                                <?php echo e($member->status === 'active' ? 'Deactivate' : 'Activate'); ?>

                                            </button>
                                        </form>
                                        <form method="POST" action="<?php echo e(route('admin.team.destroy', $member)); ?>" onsubmit="return confirm('Remove <?php echo e($member->name); ?> from your team?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button class="btn btn-xs bg-error-container text-on-error-container hover:opacity-90">Remove</button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/admin/team/index.blade.php ENDPATH**/ ?>