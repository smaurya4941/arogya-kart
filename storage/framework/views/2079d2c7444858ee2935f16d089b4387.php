<?php ($customer = $customer ?? null); ?>

<?php if($errors->any()): ?>
    <div class="rounded-lg border border-error/30 bg-error-container/40 p-3 text-sm text-on-error-container">
        <ul class="list-disc space-y-1 pl-5">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="form-label">Name <span class="text-error">*</span></label>
        <input type="text" name="name" value="<?php echo e(old('name', $customer->name ?? '')); ?>" class="form-input" required>
    </div>

    <div>
        <label class="form-label">Phone</label>
        <input type="text" name="phone" value="<?php echo e(old('phone', $customer->phone ?? '')); ?>" class="form-input">
    </div>

    <div>
        <label class="form-label">Email</label>
        <input type="email" name="email" value="<?php echo e(old('email', $customer->email ?? '')); ?>" class="form-input">
    </div>

    <div>
        <label class="form-label">Gender</label>
        <select name="gender" class="form-select">
            <option value="">—</option>
            <?php $__currentLoopData = ['male' => 'Male', 'female' => 'Female', 'other' => 'Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($value); ?>" <?php if(old('gender', $customer->gender ?? '') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div>
        <label class="form-label">Date of Birth</label>
        <input type="date" name="dob" value="<?php echo e(old('dob', optional($customer->dob ?? null)->toDateString())); ?>" class="form-input">
    </div>

    <div class="md:col-span-2">
        <label class="form-label">Address</label>
        <textarea name="address" rows="3" class="form-textarea"><?php echo e(old('address', $customer->address ?? '')); ?></textarea>
    </div>
</div>
<?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/admin/customers/_form.blade.php ENDPATH**/ ?>