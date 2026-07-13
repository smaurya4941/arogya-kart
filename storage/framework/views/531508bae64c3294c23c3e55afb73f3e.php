<?php $__env->startSection('title', 'Announcements'); ?>

<?php
    $levelBadge = ['info' => 'badge-success', 'warning' => 'badge-neutral', 'critical' => 'badge-danger'];
?>

<?php $__env->startSection('content'); ?>
    <div class="mb-4 flex items-center justify-between">
        <h2 class="section-title">Announcements</h2>
        <a href="<?php echo e(route('superadmin.announcements.create')); ?>" class="btn btn-primary btn-sm">
            <span class="material-symbols-outlined text-[18px]">add</span> New announcement
        </a>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Severity</th>
                        <th>Window</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="font-medium text-on-surface"><?php echo e($a->title); ?></div>
                                <div class="max-w-md truncate text-xs text-on-surface-variant"><?php echo e($a->body); ?></div>
                            </td>
                            <td><span class="badge <?php echo e($levelBadge[$a->level] ?? 'badge-neutral'); ?>"><?php echo e(ucfirst($a->level)); ?></span></td>
                            <td class="text-xs text-on-surface-variant">
                                <?php echo e(optional($a->starts_at)->format('d M Y') ?? 'Now'); ?> &rarr; <?php echo e(optional($a->ends_at)->format('d M Y') ?? '∞'); ?>

                            </td>
                            <td><span class="badge <?php echo e($a->is_active ? 'badge-success' : 'badge-neutral'); ?>"><?php echo e($a->is_active ? 'Active' : 'Inactive'); ?></span></td>
                            <td class="text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="<?php echo e(route('superadmin.announcements.edit', $a)); ?>" class="btn btn-xs btn-outline">Edit</a>
                                    <form method="POST" action="<?php echo e(route('superadmin.announcements.toggle', $a)); ?>" class="inline">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                        <button class="btn btn-xs bg-tertiary-container/15 text-tertiary hover:bg-tertiary-container/25"><?php echo e($a->is_active ? 'Deactivate' : 'Activate'); ?></button>
                                    </form>
                                    <form method="POST" action="<?php echo e(route('superadmin.announcements.destroy', $a)); ?>" class="inline" onsubmit="return confirm('Delete this announcement?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="btn btn-xs bg-error-container text-on-error-container hover:opacity-90">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5"><div class="empty-state">No announcements yet.</div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($announcements->hasPages()): ?>
            <div class="card-footer"><?php echo e($announcements->links()); ?></div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/superadmin/announcements/index.blade.php ENDPATH**/ ?>