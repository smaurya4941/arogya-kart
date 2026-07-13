<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="page">
        <div class="page-header">
            <h1 class="page-title">Subscription Management</h1>
        </div>

        <!-- Current Plan -->
        <div class="card card-pad">
            <h3 class="section-title mb-4">Current Subscription</h3>
            <?php if($currentSubscription && $currentSubscription->isValid()): ?>
                <?php $onTrial = $currentSubscription->onTrial(); ?>
                <div class="flex flex-col gap-3 rounded-xl border p-5 sm:flex-row sm:items-center sm:justify-between <?php echo e($onTrial ? 'border-amber-200 bg-amber-50' : 'border-tertiary/20 bg-tertiary-container/10'); ?>">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider <?php echo e($onTrial ? 'text-amber-600' : 'text-tertiary'); ?>">
                            <?php echo e($onTrial ? 'Free Trial' : 'Active Plan'); ?>

                        </p>
                        <h4 class="mt-1 text-xl font-bold text-on-surface"><?php echo e($currentSubscription->plan->name); ?> (<?php echo e(ucfirst($currentSubscription->billing_cycle)); ?>)</h4>
                        <p class="mt-2 text-sm text-on-surface-variant">
                            <?php echo e($onTrial ? 'Trial ends' : 'Valid until'); ?>:
                            <?php echo e(optional($currentSubscription->currentPeriodEnd())->format('d M, Y') ?? 'N/A'); ?>

                            <span class="ml-1 font-medium">(<?php echo e($currentSubscription->daysRemaining()); ?> days left)</span>
                        </p>
                    </div>
                    <span class="badge <?php echo e($onTrial ? 'badge-warning' : 'badge-success'); ?>"><?php echo e($onTrial ? 'Trial' : 'Active'); ?></span>
                </div>
            <?php else: ?>
                <div class="rounded-xl border border-error/30 bg-error-container/40 p-5">
                    <p class="font-bold text-on-error-container">You do not have an active subscription.</p>
                    <p class="mt-1 text-sm text-on-error-container/80">Please select a plan below to continue using the software.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Coupon -->
        <?php if($couponsEnabled): ?>
            <div class="card card-pad">
                <label for="coupon-code-input" class="section-title mb-2 block">Have a coupon?</label>
                <div class="flex max-w-sm items-center gap-2">
                    <input type="text" id="coupon-code-input" value="<?php echo e(old('coupon_code')); ?>" placeholder="Enter code (e.g. WELCOME20)"
                           class="form-input font-mono uppercase" oninput="this.value = this.value.toUpperCase()">
                </div>
                <?php $__errorArgs = ['coupon_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-2 text-sm text-error"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <p class="mt-2 text-xs text-on-surface-variant">Your discount is applied when you click <strong>Subscribe Now</strong>.</p>
            </div>
        <?php endif; ?>

        <!-- Available Plans -->
        <div>
            <h3 class="section-title mb-4">Available Plans</h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $isCurrent = $currentSubscription && $currentSubscription->plan_id == $plan->id; ?>
                    <div class="card relative overflow-hidden transition hover:shadow-md <?php echo e($isCurrent ? 'ring-2 ring-primary' : ''); ?>">
                        <?php if($plan->name == 'Professional'): ?>
                            <div class="absolute right-0 top-0 rounded-bl-lg bg-primary px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-on-primary">Most Popular</div>
                        <?php endif; ?>
                        <div class="card-pad">
                            <h4 class="text-xl font-bold text-on-surface"><?php echo e($plan->name); ?></h4>
                            <p class="h-10 text-sm text-on-surface-variant"><?php echo e($plan->description); ?></p>

                            <div class="mb-6 mt-5">
                                <span class="text-3xl font-extrabold text-on-surface">₹<?php echo e(number_format($plan->price_monthly)); ?></span>
                                <span class="text-on-surface-variant">/mo</span>
                            </div>

                            <ul class="mb-6 space-y-3 text-sm text-on-surface">
                                <li class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px] text-tertiary">check_circle</span> <?php echo e($plan->max_users); ?> Users
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px] text-tertiary">check_circle</span> <?php echo e($plan->max_branches); ?> Branches
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px] <?php echo e($plan->api_access ? 'text-tertiary' : 'text-outline-variant'); ?>"><?php echo e($plan->api_access ? 'check_circle' : 'cancel'); ?></span> API Access
                                </li>
                            </ul>

                            <form action="<?php echo e(route('admin.subscription.subscribe')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="plan_id" value="<?php echo e($plan->id); ?>">
                                <input type="hidden" name="billing_cycle" value="monthly">
                                <?php if($couponsEnabled): ?>
                                    <input type="hidden" name="coupon_code" class="coupon-code-field" value="<?php echo e(old('coupon_code')); ?>">
                                <?php endif; ?>
                                <button type="submit" class="btn w-full <?php echo e($isCurrent ? 'btn-outline cursor-not-allowed' : 'btn-primary'); ?>" <?php echo e($isCurrent ? 'disabled' : ''); ?>>
                                    <?php echo e($isCurrent ? 'Current Plan' : 'Subscribe Now'); ?>

                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <!-- Billing History -->
        <?php if(isset($invoices) && $invoices->isNotEmpty()): ?>
            <div class="card overflow-hidden">
                <div class="card-header"><h3 class="section-title">Billing History</h3></div>
                <div class="overflow-x-auto">
                    <table class="table-saas">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="font-mono-data"><?php echo e($invoice->invoice_number); ?></td>
                                    <td class="text-on-surface-variant"><?php echo e($invoice->created_at->format('d M, Y')); ?></td>
                                    <td>₹<?php echo e(number_format($invoice->total, 2)); ?></td>
                                    <td>
                                        <span class="badge <?php echo e($invoice->status === 'paid' ? 'badge-success' : ($invoice->status === 'failed' ? 'badge-danger' : 'badge-neutral')); ?>"><?php echo e(ucfirst($invoice->status)); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if($couponsEnabled): ?>
        <script>
            // Keep every plan form's hidden coupon field in sync with the shared input.
            (function () {
                const input = document.getElementById('coupon-code-input');
                if (!input) return;
                const sync = () => document.querySelectorAll('.coupon-code-field')
                    .forEach(field => field.value = input.value.trim());
                input.addEventListener('input', sync);
                sync();
            })();
        </script>
    <?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/pharmacy/subscription/index.blade.php ENDPATH**/ ?>