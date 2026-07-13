<?php $__env->startSection('title', 'Users'); ?>

<?php
    use App\Enums\UserRole;

    // Badge colour per role, keyed by the enum value.
    $roleBadge = [
        UserRole::SUPER_ADMIN->value => 'bg-primary/10 text-primary',
        UserRole::ADMIN->value       => 'bg-secondary/10 text-secondary',
        UserRole::STAFF->value       => 'bg-tertiary-container/20 text-tertiary',
        UserRole::CLIENT->value      => 'bg-outline-variant/30 text-on-surface-variant',
    ];
?>

<?php $__env->startSection('content'); ?>
    
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="section-title">All Users</h2>
            <p class="text-sm text-on-surface-variant"><?php echo e(number_format($totalUsers)); ?> accounts across every tenant.</p>
        </div>
        <a href="<?php echo e(route('superadmin.users.create')); ?>" class="btn btn-primary btn-sm">
            <span class="material-symbols-outlined text-[18px]">add</span> New user
        </a>
    </div>

    
    <div class="mb-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('superadmin.users.index', ['role' => $role->value])); ?>"
               class="card card-pad transition hover:border-primary/40 <?php echo e(request('role') === $role->value ? 'border-primary/50 ring-1 ring-primary/20' : ''); ?>">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant"><?php echo e($role->label()); ?></p>
                <p class="mt-1 text-2xl font-bold text-on-surface"><?php echo e($roleCounts[$role->value] ?? 0); ?></p>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <form method="GET" class="flex w-full flex-wrap gap-2">
                <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Search name, email, phone…" class="form-input min-w-[200px] flex-1">
                <select name="role" class="form-select w-auto">
                    <option value="">All roles</option>
                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($role->value); ?>" <?php if(request('role') === $role->value): echo 'selected'; endif; ?>><?php echo e($role->label()); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="status" class="form-select w-auto">
                    <option value="">All statuses</option>
                    <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($status); ?>" <?php if(request('status') === $status): echo 'selected'; endif; ?>><?php echo e(ucfirst($status)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select name="pharmacy_id" class="form-select w-auto">
                    <option value="">All pharmacies</option>
                    <?php $__currentLoopData = $pharmacies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pharmacy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($pharmacy->id); ?>" <?php if((string) request('pharmacy_id') === (string) $pharmacy->id): echo 'selected'; endif; ?>><?php echo e($pharmacy->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button class="btn btn-primary btn-sm">Filter</button>
                <?php if(request()->hasAny(['q', 'role', 'status', 'pharmacy_id'])): ?>
                    <a href="<?php echo e(route('superadmin.users.index')); ?>" class="btn btn-outline btn-sm">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Pharmacy</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $roleValue = $user->role?->value ?? $user->role; ?>
                        <tr>
                            <td>
                                <div class="font-medium text-on-surface">
                                    <?php echo e($user->name); ?>

                                    <?php if($user->id === auth()->id()): ?>
                                        <span class="ml-1 rounded bg-primary/10 px-1.5 py-0.5 text-[10px] font-semibold text-primary">You</span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-xs text-on-surface-variant"><?php echo e($user->email); ?></div>
                            </td>
                            <td>
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium <?php echo e($roleBadge[$roleValue] ?? 'bg-outline-variant/30 text-on-surface-variant'); ?>">
                                    <?php echo e($user->role instanceof UserRole ? $user->role->label() : ucfirst(str_replace('_', ' ', (string) $roleValue))); ?>

                                </span>
                                <?php if($user->isSuperAdmin() && ! $user->isFullSuperAdmin()): ?>
                                    <span class="ml-1 rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-semibold text-amber-700" title="<?php echo e(collect($user->admin_capabilities)->map(fn($c) => \App\Support\AdminCapability::label($c))->join(', ')); ?>">Restricted</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-on-surface-variant"><?php echo e($user->pharmacy?->name ?? '—'); ?></td>
                            <td><span class="badge <?php echo e($user->status === 'active' ? 'badge-success' : 'badge-danger'); ?>"><?php echo e(ucfirst($user->status)); ?></span></td>
                            <td class="text-on-surface-variant"><?php echo e($user->created_at?->format('d M Y') ?? '—'); ?></td>
                            <td class="text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="<?php echo e(route('superadmin.users.edit', $user)); ?>" class="btn btn-xs btn-outline">Edit</a>
                                    <?php if($user->id !== auth()->id()): ?>
                                        <form method="POST" action="<?php echo e(route('superadmin.users.toggle-status', $user)); ?>" class="inline"
                                              onsubmit="return confirm('<?php echo e($user->status === 'active' ? 'Suspend' : 'Reactivate'); ?> <?php echo e(addslashes($user->name)); ?>?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <button class="btn btn-xs <?php echo e($user->status === 'active' ? 'bg-error-container text-on-error-container hover:opacity-90' : 'bg-tertiary-container/15 text-tertiary hover:bg-tertiary-container/25'); ?>">
                                                <?php echo e($user->status === 'active' ? 'Suspend' : 'Activate'); ?>

                                            </button>
                                        </form>
                                        <form method="POST" action="<?php echo e(route('superadmin.users.destroy', $user)); ?>" class="inline"
                                              onsubmit="return confirm('Permanently delete <?php echo e(addslashes($user->name)); ?>? This cannot be undone.')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button class="btn btn-xs bg-error-container text-on-error-container hover:opacity-90">Delete</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6"><div class="empty-state">No users found.</div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($users->hasPages()): ?>
            <div class="card-footer"><?php echo e($users->links()); ?></div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/superadmin/users/index.blade.php ENDPATH**/ ?>