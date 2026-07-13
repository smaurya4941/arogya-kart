<?php
    $currentPosition = old('position', isset($member) ? $member->roles->pluck('name')->first() : null);
    $isEdit = isset($member);
?>

<?php if($errors->any()): ?>
    <div class="mb-4 rounded-lg border border-error/30 bg-error-container/40 p-3 text-sm text-on-error-container">
        <ul class="list-disc space-y-1 pl-5">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <label class="form-label">Full name</label>
        <input type="text" name="name" value="<?php echo e(old('name', $member->name ?? '')); ?>" required class="form-input">
    </div>
    <div>
        <label class="form-label">Email</label>
        <input type="email" name="email" value="<?php echo e(old('email', $member->email ?? '')); ?>" required class="form-input">
    </div>
    <div>
        <label class="form-label">Phone <span class="text-outline">(optional)</span></label>
        <input type="text" name="phone" value="<?php echo e(old('phone', $member->phone ?? '')); ?>" class="form-input">
    </div>
    <div>
        <label class="form-label">Position</label>
        <select name="position" required class="form-select">
            <option value="">Select a position…</option>
            <?php $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($position); ?>" <?php if($currentPosition === $position): echo 'selected'; endif; ?>><?php echo e($position); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div>
        <label class="form-label">
            Password <?php if($isEdit): ?><span class="text-outline">(leave blank to keep)</span><?php endif; ?>
        </label>
        <input type="password" name="password" <?php echo e($isEdit ? '' : 'required'); ?> autocomplete="new-password" class="form-input">
    </div>
    <div>
        <label class="form-label">Confirm password</label>
        <input type="password" name="password_confirmation" autocomplete="new-password" class="form-input">
    </div>
</div>

<div class="mt-6 flex gap-2">
    <button class="btn btn-primary"><?php echo e($isEdit ? 'Save changes' : 'Add member'); ?></button>
    <a href="<?php echo e(route('admin.team.index')); ?>" class="btn btn-outline">Cancel</a>
</div>
<?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/admin/team/_form.blade.php ENDPATH**/ ?>