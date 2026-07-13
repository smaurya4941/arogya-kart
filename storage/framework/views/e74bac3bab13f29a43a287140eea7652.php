<?php $__env->startSection('title', 'Activity Log'); ?>

<?php $__env->startSection('content'); ?>
    <div class="card overflow-hidden">
        <div class="card-header flex-col items-stretch gap-3">
            <div class="flex items-center gap-2 text-sm">
                <a href="<?php echo e(route('superadmin.audit.index', ['filter' => 'impersonation'])); ?>"
                   class="btn btn-sm <?php echo e($filter === 'impersonation' ? 'btn-primary' : 'btn-outline'); ?>">Impersonation</a>
                <a href="<?php echo e(route('superadmin.audit.index', ['filter' => 'all'])); ?>"
                   class="btn btn-sm <?php echo e($filter === 'all' ? 'btn-primary' : 'btn-outline'); ?>">All activity</a>
            </div>
            <form method="GET" class="flex w-full flex-wrap items-center gap-2">
                <input type="hidden" name="filter" value="<?php echo e($filter); ?>">
                <select name="user_id" class="form-select w-auto">
                    <option value="">All actors</option>
                    <?php $__currentLoopData = $actors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $actor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($actor->id); ?>" <?php if((string) request('user_id') === (string) $actor->id): echo 'selected'; endif; ?>><?php echo e($actor->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="action" class="form-select w-auto">
                    <option value="">All actions</option>
                    <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($action); ?>" <?php if(request('action') === $action): echo 'selected'; endif; ?>><?php echo e(str_replace('_', ' ', $action)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <input type="date" name="from" value="<?php echo e(request('from')); ?>" class="form-input w-auto" title="From">
                <input type="date" name="to" value="<?php echo e(request('to')); ?>" class="form-input w-auto" title="To">
                <button class="btn btn-primary btn-sm">Filter</button>
                <a href="<?php echo e(route('superadmin.audit.export', request()->query())); ?>" class="btn btn-outline btn-sm ml-auto">
                    <span class="material-symbols-outlined text-[18px]">download</span> Export CSV
                </a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>When</th>
                        <th>Actor</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="align-top">
                            <td class="whitespace-nowrap text-on-surface-variant"><?php echo e($log->created_at->format('d M Y, H:i')); ?></td>
                            <td><?php echo e($log->user?->name ?? 'System'); ?></td>
                            <td>
                                <span class="badge <?php echo e(str_starts_with($log->action, 'impersonation') ? 'badge-info' : 'badge-neutral'); ?>">
                                    <?php echo e(str_replace('_', ' ', $log->action)); ?>

                                </span>
                            </td>
                            <td class="text-on-surface-variant">
                                <?php if(is_array($log->meta)): ?>
                                    <?php $__currentLoopData = $log->meta; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="mr-3"><span class="text-outline"><?php echo e(str_replace('_',' ',$k)); ?>:</span> <?php echo e(is_scalar($v) ? $v : json_encode($v)); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </td>
                            <td class="whitespace-nowrap text-outline"><?php echo e($log->ip_address); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5"><div class="empty-state">No activity recorded.</div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($logs->hasPages()): ?>
            <div class="card-footer"><?php echo e($logs->links()); ?></div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/superadmin/audit/index.blade.php ENDPATH**/ ?>