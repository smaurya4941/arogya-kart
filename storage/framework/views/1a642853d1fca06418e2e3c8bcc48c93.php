<?php $__env->startSection('title', 'System Health'); ?>

<?php $__env->startSection('content'); ?>
    
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        <?php $__currentLoopData = $checks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $check): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card card-pad flex items-start gap-3">
                <span class="material-symbols-outlined text-[22px] <?php echo e($check['ok'] ? 'text-tertiary' : 'text-error'); ?>">
                    <?php echo e($check['ok'] ? 'check_circle' : 'error'); ?>

                </span>
                <div>
                    <p class="font-medium text-on-surface"><?php echo e($check['label']); ?></p>
                    <p class="text-xs text-on-surface-variant"><?php echo e($check['detail']); ?></p>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="card card-pad">
            <h2 class="section-title mb-4">Background queue</h2>
            <div class="flex gap-6">
                <div>
                    <p class="text-3xl font-bold text-primary"><?php echo e($queue['pending']); ?></p>
                    <p class="text-xs text-on-surface-variant">Pending jobs</p>
                </div>
                <div>
                    <p class="text-3xl font-bold <?php echo e($queue['failed'] > 0 ? 'text-error' : 'text-on-surface'); ?>"><?php echo e($queue['failed']); ?></p>
                    <p class="text-xs text-on-surface-variant">Failed jobs</p>
                </div>
            </div>
            <?php if($queue['failed'] > 0): ?>
                <div class="mt-4 flex gap-2">
                    <form method="POST" action="<?php echo e(route('superadmin.system.failed.retry')); ?>">
                        <?php echo csrf_field(); ?>
                        <button class="btn btn-sm btn-outline">Retry all</button>
                    </form>
                    <form method="POST" action="<?php echo e(route('superadmin.system.failed.flush')); ?>" onsubmit="return confirm('Permanently clear the failed-jobs log?')">
                        <?php echo csrf_field(); ?>
                        <button class="btn btn-sm bg-error-container text-on-error-container hover:opacity-90">Flush log</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <div class="card card-pad lg:col-span-2">
            <h2 class="section-title mb-4">Environment</h2>
            <dl class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm sm:grid-cols-3">
                <?php $__currentLoopData = $environment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <dt class="text-xs text-on-surface-variant"><?php echo e($label); ?></dt>
                        <dd class="font-medium text-on-surface"><?php echo e($value); ?></dd>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </dl>
        </div>
    </div>

    
    <div class="card mt-4 overflow-hidden">
        <div class="card-header"><h2 class="section-title">Recent failed jobs</h2></div>
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr><th>Job</th><th>Queue</th><th>Failed at</th><th>Error</th></tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $failedJobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="font-medium"><?php echo e($job->name); ?></td>
                            <td class="text-on-surface-variant"><?php echo e($job->queue); ?></td>
                            <td class="text-on-surface-variant"><?php echo e(\Illuminate\Support\Carbon::parse($job->failed_at)->format('d M Y, H:i')); ?></td>
                            <td class="max-w-md truncate text-xs text-error"><?php echo e($job->error); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4"><div class="empty-state">No failed jobs. 🎉</div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/superadmin/system/index.blade.php ENDPATH**/ ?>