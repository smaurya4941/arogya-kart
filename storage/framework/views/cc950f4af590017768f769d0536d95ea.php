<?php ($supplier = $supplier ?? null); ?>

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
        <input type="text" name="name" value="<?php echo e(old('name', $supplier->name ?? '')); ?>" class="form-input" required>
    </div>

    <div>
        <label class="form-label">Company Name</label>
        <input type="text" name="company_name" value="<?php echo e(old('company_name', $supplier->company_name ?? '')); ?>" class="form-input">
    </div>

    <div>
        <label class="form-label">Contact Person</label>
        <input type="text" name="contact_person" value="<?php echo e(old('contact_person', $supplier->contact_person ?? '')); ?>" class="form-input">
    </div>

    <div>
        <label class="form-label">Phone</label>
        <input type="text" name="phone" value="<?php echo e(old('phone', $supplier->phone ?? '')); ?>" class="form-input">
    </div>

    <div>
        <label class="form-label">Email</label>
        <input type="email" name="email" value="<?php echo e(old('email', $supplier->email ?? '')); ?>" class="form-input">
    </div>

    <div>
        <label class="form-label">GST Number</label>
        <input type="text" name="gst_number" value="<?php echo e(old('gst_number', $supplier->gst_number ?? '')); ?>" class="form-input">
    </div>

    <div>
        <label class="form-label">City</label>
        <input type="text" name="city" value="<?php echo e(old('city', $supplier->city ?? '')); ?>" class="form-input">
    </div>

    <div>
        <label class="form-label">State</label>
        <input type="text" name="state" value="<?php echo e(old('state', $supplier->state ?? '')); ?>" class="form-input">
    </div>

    <div class="md:col-span-2">
        <label class="form-label">Address</label>
        <textarea name="address" rows="3" class="form-textarea"><?php echo e(old('address', $supplier->address ?? '')); ?></textarea>
    </div>

    <div class="md:col-span-2">
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" class="rounded border-outline-variant text-primary focus:ring-primary/30" <?php if(old('is_active', $supplier->is_active ?? true)): echo 'checked'; endif; ?>>
            <span class="text-sm font-medium text-on-surface">Active</span>
        </label>
    </div>
</div>
<?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/admin/suppliers/_form.blade.php ENDPATH**/ ?>