<?php $__env->startSection('title', $customer->name); ?>

<?php $__env->startSection('content'); ?>
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title"><?php echo e($customer->name); ?></h1>
            <p class="page-subtitle">Customer profile &amp; purchase history.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.sales.create', ['customer_id' => $customer->id])); ?>" class="btn btn-primary">
                <span class="material-symbols-outlined text-[18px]">point_of_sale</span> New Sale
            </a>
            <a href="<?php echo e(route('admin.customers.edit', $customer)); ?>" class="btn btn-outline">Edit</a>
            <a href="<?php echo e(route('admin.customers.index')); ?>" class="btn btn-outline">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="card card-pad space-y-3">
            <h2 class="section-title">Details</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-on-surface-variant">Phone</dt><dd><?php echo e($customer->phone ?? '-'); ?></dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Email</dt><dd><?php echo e($customer->email ?? '-'); ?></dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Gender</dt><dd class="capitalize"><?php echo e($customer->gender ?? '-'); ?></dd></div>
                <div class="flex justify-between"><dt class="text-on-surface-variant">Date of Birth</dt><dd><?php echo e(optional($customer->dob)->format('d M Y') ?? '-'); ?></dd></div>
                <div class="pt-2"><dt class="mb-1 text-on-surface-variant">Address</dt><dd><?php echo e($customer->address ?? '-'); ?></dd></div>
            </dl>
        </div>

        <div class="grid grid-cols-1 gap-4 content-start sm:grid-cols-3 lg:col-span-2">
            <div class="card card-pad">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Total Bills</p>
                <p class="mt-1 text-2xl font-bold text-on-surface"><?php echo e($customer->sales_count); ?></p>
            </div>
            <div class="card card-pad">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Lifetime Value</p>
                <p class="mt-1 text-2xl font-bold text-on-surface">₹<?php echo e(number_format((float) $customer->sales_total, 2)); ?></p>
            </div>
            <div class="card card-pad">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Outstanding Due</p>
                <p class="mt-1 text-2xl font-bold text-error">₹<?php echo e(number_format((float) $customer->sales_due, 2)); ?></p>
            </div>

            <div class="card overflow-hidden sm:col-span-3">
                <div class="card-header"><h2 class="section-title">Recent Sales</h2></div>
                <div class="overflow-x-auto">
                    <table class="table-saas">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Date</th>
                                <th class="text-right">Total</th>
                                <th class="text-right">Due</th>
                                <th>Status</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $recentSales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="font-medium"><?php echo e($sale->invoice_number); ?></td>
                                    <td class="text-on-surface-variant"><?php echo e($sale->sale_date->format('d M Y, h:i A')); ?></td>
                                    <td class="text-right">₹<?php echo e(number_format((float) $sale->total_amount, 2)); ?></td>
                                    <td class="text-right <?php echo e($sale->due_amount > 0 ? 'text-error' : ''); ?>">₹<?php echo e(number_format((float) $sale->due_amount, 2)); ?></td>
                                    <td><span class="badge capitalize <?php echo e($sale->paymentStatusBadge()); ?>"><?php echo e($sale->payment_status); ?></span></td>
                                    <td class="text-right">
                                        <a class="btn-icon ml-auto" title="View" href="<?php echo e(route('admin.sales.show', $sale)); ?>">
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state">
                                            <span class="material-symbols-outlined text-[32px] opacity-40">receipt_long</span>
                                            No sales yet.
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php if(! $customer->sales_count): ?>
        <form method="POST" action="<?php echo e(route('admin.customers.destroy', $customer)); ?>"
              onsubmit="return confirm('Delete this customer?')">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button class="text-sm font-medium text-error hover:underline">Delete customer</button>
        </form>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/admin/customers/show.blade.php ENDPATH**/ ?>