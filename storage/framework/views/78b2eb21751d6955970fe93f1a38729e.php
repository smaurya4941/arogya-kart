<?php $__env->startSection('title', 'Invoices'); ?>

<?php
    use App\Models\Invoice;
    $badge = [
        Invoice::STATUS_PAID     => 'badge-success',
        Invoice::STATUS_PENDING  => 'badge-neutral',
        Invoice::STATUS_FAILED   => 'badge-danger',
        Invoice::STATUS_REFUNDED => 'badge-neutral',
        Invoice::STATUS_VOID     => 'badge-danger',
    ];
?>

<?php $__env->startSection('content'); ?>
    
    <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Invoices</p>
            <p class="mt-1 text-2xl font-bold text-on-surface"><?php echo e(number_format($totals->count)); ?></p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Billed</p>
            <p class="mt-1 text-2xl font-bold text-primary">₹<?php echo e(number_format($totals->billed, 2)); ?></p>
        </div>
        <div class="card card-pad">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Collected (paid)</p>
            <p class="mt-1 text-2xl font-bold text-tertiary">₹<?php echo e(number_format($totals->collected, 2)); ?></p>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header">
            <form method="GET" class="flex w-full flex-wrap items-center gap-2">
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
                <input type="date" name="from" value="<?php echo e(request('from')); ?>" class="form-input w-auto" title="From">
                <input type="date" name="to" value="<?php echo e(request('to')); ?>" class="form-input w-auto" title="To">
                <button class="btn btn-primary btn-sm">Filter</button>
                <?php if(request()->hasAny(['status', 'pharmacy_id', 'from', 'to'])): ?>
                    <a href="<?php echo e(route('superadmin.invoices.index')); ?>" class="btn btn-outline btn-sm">Reset</a>
                <?php endif; ?>
                <a href="<?php echo e(route('superadmin.invoices.export', request()->query())); ?>" class="btn btn-outline btn-sm ml-auto">
                    <span class="material-symbols-outlined text-[18px]">download</span> Export CSV
                </a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="table-saas">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Pharmacy</th>
                        <th>Plan</th>
                        <th class="text-right">Total</th>
                        <th>Status</th>
                        <th>Issued</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="font-mono-data"><?php echo e($invoice->invoice_number); ?></td>
                            <td><?php echo e($invoice->pharmacy?->name ?? '—'); ?></td>
                            <td class="text-on-surface-variant"><?php echo e($invoice->subscription?->plan?->name ?? '—'); ?></td>
                            <td class="text-right">₹<?php echo e(number_format($invoice->total, 2)); ?></td>
                            <td><span class="badge <?php echo e($badge[$invoice->status] ?? 'badge-neutral'); ?>"><?php echo e(ucfirst($invoice->status)); ?></span></td>
                            <td class="text-on-surface-variant"><?php echo e($invoice->created_at->format('d M Y')); ?></td>
                            <td class="text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="<?php echo e(route('superadmin.invoices.pdf', $invoice)); ?>" target="_blank" class="btn btn-xs btn-outline">PDF</a>
                                    <?php if($invoice->status !== Invoice::STATUS_PAID && $invoice->status !== Invoice::STATUS_VOID && $invoice->status !== Invoice::STATUS_REFUNDED): ?>
                                        <form method="POST" action="<?php echo e(route('superadmin.invoices.mark-paid', $invoice)); ?>" class="inline">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <button class="btn btn-xs bg-tertiary-container/15 text-tertiary hover:bg-tertiary-container/25">Mark paid</button>
                                        </form>
                                        <form method="POST" action="<?php echo e(route('superadmin.invoices.void', $invoice)); ?>" class="inline"
                                              onsubmit="return confirm('Void invoice <?php echo e($invoice->invoice_number); ?>?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <button class="btn btn-xs bg-error-container text-on-error-container hover:opacity-90">Void</button>
                                        </form>
                                    <?php elseif($invoice->status === Invoice::STATUS_PAID): ?>
                                        <form method="POST" action="<?php echo e(route('superadmin.invoices.refund', $invoice)); ?>" class="inline"
                                              onsubmit="return confirm('Mark invoice <?php echo e($invoice->invoice_number); ?> as refunded?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <button class="btn btn-xs bg-error-container text-on-error-container hover:opacity-90">Refund</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="7"><div class="empty-state">No invoices found.</div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($invoices->hasPages()): ?>
            <div class="card-footer"><?php echo e($invoices->links()); ?></div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/superadmin/invoices/index.blade.php ENDPATH**/ ?>