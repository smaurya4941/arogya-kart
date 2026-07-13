<?php ($isNew = ! $pharmacy->exists); ?>

<div class="space-y-6">
    
    <div>
        <h3 class="section-title mb-3">Pharmacy details</h3>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label class="form-label">Pharmacy name</label>
                <input type="text" name="name" value="<?php echo e(old('name', $pharmacy->name)); ?>" required class="form-input">
            </div>
            <div>
                <label class="form-label">Owner name</label>
                <input type="text" name="owner_name" value="<?php echo e(old('owner_name', $pharmacy->owner_name)); ?>" required class="form-input">
            </div>
            <div>
                <label class="form-label">Phone</label>
                <input type="text" name="phone" value="<?php echo e(old('phone', $pharmacy->phone)); ?>" class="form-input">
            </div>
            <div>
                <label class="form-label">GST number</label>
                <input type="text" name="gst" value="<?php echo e(old('gst', $pharmacy->gst)); ?>" class="form-input">
            </div>
            <div>
                <label class="form-label">Drug license number</label>
                <input type="text" name="drug_license_number" value="<?php echo e(old('drug_license_number', $pharmacy->drug_license_number)); ?>" class="form-input">
            </div>
            <div>
                <label class="form-label">PAN number</label>
                <input type="text" name="pan_number" value="<?php echo e(old('pan_number', $pharmacy->pan_number)); ?>" class="form-input">
            </div>
            <div class="md:col-span-2">
                <label class="form-label">Address</label>
                <textarea name="address" rows="2" class="form-input"><?php echo e(old('address', $pharmacy->address)); ?></textarea>
            </div>
            <div>
                <label class="form-label">City</label>
                <input type="text" name="city" value="<?php echo e(old('city', $pharmacy->city)); ?>" class="form-input">
            </div>
            <div>
                <label class="form-label">State</label>
                <input type="text" name="state" value="<?php echo e(old('state', $pharmacy->state)); ?>" class="form-input">
            </div>
            <div>
                <label class="form-label">Pincode</label>
                <input type="text" name="pincode" value="<?php echo e(old('pincode', $pharmacy->pincode)); ?>" class="form-input">
            </div>

            <?php if (! ($isNew)): ?>
                <div>
                    <label class="form-label">Contact email</label>
                    <input type="email" name="email" value="<?php echo e(old('email', $pharmacy->email)); ?>" class="form-input">
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="<?php echo e(\App\Models\Pharmacy::STATUS_ACTIVE); ?>" <?php if(old('status', $pharmacy->status) === \App\Models\Pharmacy::STATUS_ACTIVE): echo 'selected'; endif; ?>>Active</option>
                        <option value="<?php echo e(\App\Models\Pharmacy::STATUS_SUSPENDED); ?>" <?php if(old('status', $pharmacy->status) === \App\Models\Pharmacy::STATUS_SUSPENDED): echo 'selected'; endif; ?>>Suspended</option>
                    </select>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if($isNew): ?>
        
        <div class="border-t border-outline-variant pt-6">
            <h3 class="section-title mb-1">Owner account</h3>
            <p class="mb-3 text-xs text-on-surface-variant">The owner logs in with this email and becomes the pharmacy's admin. It also serves as the pharmacy contact.</p>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="form-label">Owner email</label>
                    <input type="email" name="owner_email" value="<?php echo e(old('owner_email')); ?>" required class="form-input">
                </div>
                <div>
                    <label class="form-label">Password</label>
                    <input type="password" name="owner_password" autocomplete="new-password" required class="form-input">
                </div>
                <div>
                    <label class="form-label">Confirm password</label>
                    <input type="password" name="owner_password_confirmation" autocomplete="new-password" required class="form-input">
                </div>
            </div>
        </div>

        
        <div class="border-t border-outline-variant pt-6">
            <h3 class="section-title mb-1">Starting plan</h3>
            <p class="mb-3 text-xs text-on-surface-variant">Optionally start a free trial so the tenant has working access immediately. Leave as "No subscription" to onboard without one.</p>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="form-label">Plan</label>
                    <select name="plan_id" class="form-select">
                        <option value="">— No subscription —</option>
                        <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($plan->id); ?>" <?php if((string) old('plan_id') === (string) $plan->id): echo 'selected'; endif; ?>><?php echo e($plan->name); ?> (₹<?php echo e(number_format($plan->price_monthly, 0)); ?>/mo)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">Trial length (days)</label>
                    <input type="number" name="trial_days" min="1" max="365" value="<?php echo e(old('trial_days', $trialDays)); ?>" class="form-input">
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="mt-6 flex gap-2">
    <button class="btn btn-primary"><?php echo e($isNew ? 'Onboard pharmacy' : 'Save changes'); ?></button>
    <a href="<?php echo e($isNew ? route('superadmin.pharmacies.index') : route('superadmin.pharmacies.show', $pharmacy)); ?>" class="btn btn-outline">Cancel</a>
</div>
<?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/superadmin/pharmacies/_form.blade.php ENDPATH**/ ?>