<?php
    use App\Enums\UserRole;
    use App\Support\AdminCapability;
    $currentRole = old('role', $user->role?->value ?? $user->role);

    // Capability editor state — only a full platform owner may manage these.
    $canManageCaps = auth()->user()->isFullSuperAdmin();
    $isFullDefault = $user->exists ? $user->isFullSuperAdmin() : true;
    $currentCaps   = old('admin_capabilities', ($user->exists && ! $user->isFullSuperAdmin()) ? ($user->admin_capabilities ?? []) : []);
?>

<div x-data="{ role: '<?php echo e($currentRole ?: UserRole::ADMIN->value); ?>', full: <?php echo e($isFullDefault ? 'true' : 'false'); ?> }" class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="form-label">Full name</label>
        <input type="text" name="name" value="<?php echo e(old('name', $user->name)); ?>" required class="form-input">
    </div>
    <div>
        <label class="form-label">Email</label>
        <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" required class="form-input">
    </div>

    <div>
        <label class="form-label">Phone</label>
        <input type="text" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>" class="form-input">
    </div>
    <div>
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($status); ?>" <?php if(old('status', $user->status) === $status): echo 'selected'; endif; ?>><?php echo e(ucfirst($status)); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div>
        <label class="form-label">Role</label>
        <select name="role" x-model="role" class="form-select">
            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($role->value); ?>" <?php if($currentRole === $role->value): echo 'selected'; endif; ?>><?php echo e($role->label()); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    
    <div x-show="role !== '<?php echo e(UserRole::SUPER_ADMIN->value); ?>'" x-cloak>
        <label class="form-label">Pharmacy</label>
        <select name="pharmacy_id" class="form-select">
            <option value="">— Select pharmacy —</option>
            <?php $__currentLoopData = $pharmacies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pharmacy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($pharmacy->id); ?>" <?php if((string) old('pharmacy_id', $user->pharmacy_id) === (string) $pharmacy->id): echo 'selected'; endif; ?>><?php echo e($pharmacy->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <p class="mt-1 text-xs text-on-surface-variant">Required for Pharmacy Owner, Staff and Customer accounts.</p>
    </div>

    
    <?php if($canManageCaps): ?>
        <div x-show="role === '<?php echo e(UserRole::SUPER_ADMIN->value); ?>'" x-cloak class="md:col-span-2">
            <label class="form-label">Platform access</label>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="admin_full" value="1" x-model="full" class="rounded border-outline-variant text-primary focus:ring-primary/30">
                Full access (all capabilities)
            </label>
            <div x-show="!full" x-cloak class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                <?php $__currentLoopData = AdminCapability::catalogue(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="flex items-start gap-2 rounded-lg border border-outline-variant/60 p-2 text-sm">
                        <input type="checkbox" name="admin_capabilities[]" value="<?php echo e($key); ?>" <?php if(in_array($key, $currentCaps)): echo 'checked'; endif; ?> class="mt-0.5 rounded border-outline-variant text-primary focus:ring-primary/30">
                        <span>
                            <span class="font-medium"><?php echo e($meta['label']); ?></span>
                            <span class="block text-xs text-on-surface-variant"><?php echo e($meta['description']); ?></span>
                        </span>
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <p class="mt-1 text-xs text-on-surface-variant">Uncheck "Full access" to restrict this admin (e.g. a support operator) to specific sections only.</p>
        </div>
    <?php endif; ?>

    <div>
        <label class="form-label">Password <?php echo e($user->exists ? '(leave blank to keep current)' : ''); ?></label>
        <input type="password" name="password" autocomplete="new-password" <?php if(! $user->exists): echo 'required'; endif; ?> class="form-input">
    </div>
    <div>
        <label class="form-label">Confirm password</label>
        <input type="password" name="password_confirmation" autocomplete="new-password" class="form-input">
    </div>
</div>

<div class="mt-6 flex gap-2">
    <button class="btn btn-primary"><?php echo e($user->exists ? 'Save changes' : 'Create user'); ?></button>
    <a href="<?php echo e(route('superadmin.users.index')); ?>" class="btn btn-outline">Cancel</a>
</div>
<?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/superadmin/users/_form.blade.php ENDPATH**/ ?>