<?php $__env->startSection('title', 'Sales'); ?>

<?php $__env->startSection('content'); ?>
<div class="page">
    <div class="page-header">
        <div>
            <h1 class="page-title">Sales</h1>
            <p class="page-subtitle">Every bill rung up at the counter.</p>
        </div>
        <a href="<?php echo e(route('admin.sales.create')); ?>" class="btn btn-primary">
            <span class="material-symbols-outlined text-[18px]">point_of_sale</span> New Sale (POS)
        </a>
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Bills</p>
            <p class="mt-1 text-2xl font-bold text-on-surface"><?php echo e(number_format((int) ($totals->count ?? 0))); ?></p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Total</p>
            <p class="mt-1 text-2xl font-bold text-on-surface">₹<?php echo e(number_format((float) ($totals->total ?? 0), 2)); ?></p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Collected</p>
            <p class="mt-1 text-2xl font-bold text-tertiary">₹<?php echo e(number_format((float) ($totals->paid ?? 0), 2)); ?></p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Outstanding</p>
            <p class="mt-1 text-2xl font-bold text-error">₹<?php echo e(number_format((float) ($totals->due ?? 0), 2)); ?></p>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="<?php echo e(route('admin.sales.index')); ?>" class="card card-pad">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-6">
            <div class="md:col-span-2">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Invoice # or customer" class="form-input">
            </div>
            <div>
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <?php $__currentLoopData = ['paid' => 'Paid', 'partial' => 'Partial', 'unpaid' => 'Unpaid']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>" <?php if(request('status') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="form-label">Method</label>
                <select name="method" class="form-select">
                    <option value="">All</option>
                    <?php $__currentLoopData = ['cash' => 'Cash', 'card' => 'Card', 'upi' => 'UPI', 'credit' => 'Credit']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>" <?php if(request('method') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="form-label">From</label>
                <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="form-input">
            </div>
            <div>
                <label class="form-label">To</label>
                <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" class="form-input">
            </div>
        </div>
        <div class="mt-3 flex gap-2">
            <button class="btn btn-primary btn-sm">Apply</button>
            <a href="<?php echo e(route('admin.sales.index')); ?>" class="btn btn-outline btn-sm">Reset</a>
        </div>
    </form>

    <!-- Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Cashier</th>
                        <th>Method</th>
                        <th class="text-right">Total</th>
                        <th class="text-right">Due</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="font-medium"><?php echo e($sale->invoice_number); ?></td>
                            <td class="text-on-surface-variant"><?php echo e($sale->sale_date->format('d M Y, h:i A')); ?></td>
                            <td class="text-on-surface-variant"><?php echo e($sale->customer?->name ?? 'Walk-in'); ?></td>
                            <td class="text-on-surface-variant"><?php echo e($sale->cashier?->name ?? '-'); ?></td>
                            <td class="capitalize"><?php echo e($sale->payment_method); ?></td>
                            <td class="text-right font-semibold">₹<?php echo e(number_format((float) $sale->total_amount, 2)); ?></td>
                            <td class="text-right <?php echo e($sale->due_amount > 0 ? 'text-error' : ''); ?>">₹<?php echo e(number_format((float) $sale->due_amount, 2)); ?></td>
                            <td>
                                <span class="badge capitalize <?php echo e($sale->paymentStatusBadge()); ?>"><?php echo e($sale->payment_status); ?></span>
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-1">
                                    <a class="btn-icon" title="View" href="<?php echo e(route('admin.sales.show', $sale)); ?>">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                    <a class="btn-icon" title="Print" href="<?php echo e(route('admin.sales.invoice', $sale)); ?>" target="_blank">
                                        <span class="material-symbols-outlined text-[18px]">print</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9">
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
        <?php if($sales->hasPages()): ?>
            <div class="card-footer"><?php echo e($sales->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/admin/sales/index.blade.php ENDPATH**/ ?>