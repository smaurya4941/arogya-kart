<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('subtitle', now()->format('l, F j, Y')); ?>

<?php
    $metrics = [
        [
            'label' => "Today's Sales", 'icon' => 'payments', 'tone' => 'text-primary bg-primary/10',
            'value' => '₹'.number_format($todayRevenue ?? 0, 2),
            'meta' => ($todayInvoices ?? 0).' orders',
        ],
        [
            'label' => 'Low Stock', 'icon' => 'inventory', 'tone' => 'text-secondary bg-secondary/10',
            'value' => str_pad($lowStockCount ?? 0, 2, '0', STR_PAD_LEFT),
            'meta' => ($lowStockCount ?? 0) > 0 ? 'Needs attention' : 'Healthy',
        ],
        [
            'label' => 'Expiring Soon', 'icon' => 'event_busy', 'tone' => 'text-error bg-error-container/40',
            'value' => str_pad($expiringCount ?? 0, 2, '0', STR_PAD_LEFT),
            'meta' => 'Next 90 days',
        ],
        [
            'label' => 'Total Medicines', 'icon' => 'medication', 'tone' => 'text-tertiary bg-tertiary/10',
            'value' => str_pad($totalMedicinesCount ?? 0, 2, '0', STR_PAD_LEFT),
            'meta' => 'Active: '.($activeMedicines ?? 0),
        ],
    ];
?>

<?php $__env->startSection('content'); ?>
<div class="page">
    <!-- Header -->
    <div class="page-header">
        <div>
            <h2 class="page-title">Operations Dashboard</h2>
            <p class="page-subtitle"><?php echo e(auth()->user()->pharmacy->name ?? 'Your pharmacy'); ?> · <?php echo e(now()->format('M j, Y')); ?></p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.pos.index')); ?>" class="btn btn-outline">
                <span class="material-symbols-outlined text-[18px]">receipt_long</span> New Bill
            </a>
            <a href="<?php echo e(route('admin.products.create')); ?>" class="btn btn-outline">
                <span class="material-symbols-outlined text-[18px]">add_business</span> Add Inventory
            </a>
            <a href="<?php echo e(route('admin.purchases.create')); ?>" class="btn btn-primary">
                <span class="material-symbols-outlined text-[18px]">payments</span> Purchase
            </a>
        </div>
    </div>

    <!-- Metric cards -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <?php $__currentLoopData = $metrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card card-pad">
                <div class="mb-3 flex items-center justify-between">
                    <span class="icon-tile <?php echo e($m['tone']); ?>">
                        <span class="material-symbols-outlined text-[20px]"><?php echo e($m['icon']); ?></span>
                    </span>
                    <span class="text-[11px] font-medium text-on-surface-variant"><?php echo e($m['meta']); ?></span>
                </div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant"><?php echo e($m['label']); ?></p>
                <h3 class="mt-0.5 text-2xl font-bold tracking-tight text-on-surface"><?php echo e($m['value']); ?></h3>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Main grid -->
    <div class="grid grid-cols-12 gap-4">
        <!-- Sales overview -->
        <div class="card col-span-12 lg:col-span-8">
            <div class="card-header">
                <div>
                    <h4 class="section-title">Sales Overview</h4>
                    <p class="text-xs text-on-surface-variant">Weekly distribution</p>
                </div>
                <div class="flex gap-1 rounded-lg bg-surface-container-low p-0.5">
                    <button class="rounded-md bg-white px-2.5 py-1 text-xs font-semibold text-primary shadow-sm">7 Days</button>
                    <button class="rounded-md px-2.5 py-1 text-xs font-medium text-on-surface-variant hover:bg-white/60">Month</button>
                </div>
            </div>
            <div class="card-pad">
                <div class="flex h-52 items-end justify-between gap-2 sm:gap-4">
                    <?php $heights = [60, 45, 75, 90, 55, 85, 100]; $days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']; ?>
                    <?php $__currentLoopData = $heights; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex flex-1 flex-col items-center gap-2">
                            <div class="group relative w-full overflow-hidden rounded-t-md bg-primary/15" style="height: <?php echo e($h); ?>%">
                                <div class="absolute bottom-0 w-full bg-primary transition-all duration-500 group-hover:h-full" style="height: 80%"></div>
                            </div>
                            <span class="text-[10px] font-medium text-on-surface-variant"><?php echo e($days[$i]); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        <!-- Critical inventory -->
        <div class="card col-span-12 flex flex-col lg:col-span-4">
            <div class="card-header">
                <h4 class="section-title">Critical Inventory</h4>
                <?php if(count($lowStockMedicines ?? []) > 0 || count($expiringMedicines ?? []) > 0): ?>
                    <span class="badge badge-danger">Urgent</span>
                <?php else: ?>
                    <span class="badge badge-success">Clear</span>
                <?php endif; ?>
            </div>
            <div class="flex-1 space-y-2 p-3">
                <?php $__empty_1 = true; $__currentLoopData = collect($expiringMedicines ?? [])->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center gap-3 rounded-lg bg-surface-container-low px-3 py-2">
                        <span class="icon-tile h-9 w-9 bg-error-container/40 text-error">
                            <span class="material-symbols-outlined text-[18px]">event_busy</span>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-on-surface"><?php echo e($batch->product->name); ?></p>
                            <p class="text-xs text-on-surface-variant">Expiring <?php echo e(\Carbon\Carbon::parse($batch->expiry_date)->diffForHumans()); ?></p>
                        </div>
                        <span class="font-mono-data text-error"><?php echo e($batch->quantity); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <?php endif; ?>

                <?php $__empty_1 = true; $__currentLoopData = collect($lowStockMedicines ?? [])->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $med): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center gap-3 rounded-lg bg-surface-container-low px-3 py-2">
                        <span class="icon-tile h-9 w-9 bg-amber-100 text-amber-600">
                            <span class="material-symbols-outlined text-[18px]">warning</span>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-on-surface"><?php echo e($med->name); ?></p>
                            <p class="text-xs text-on-surface-variant">Below reorder level</p>
                        </div>
                        <span class="font-mono-data text-secondary"><?php echo e($med->total_stock); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <?php if(count($expiringMedicines ?? []) === 0): ?>
                        <div class="empty-state">
                            <span class="material-symbols-outlined text-[40px] opacity-40">check_circle</span>
                            <p class="text-sm">No critical alerts.</p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <a href="<?php echo e(route('admin.products.index')); ?>" class="btn btn-ghost w-full">View Inventory</a>
            </div>
        </div>

        <!-- Recent sales -->
        <div class="card col-span-12 overflow-hidden">
            <div class="card-header">
                <h4 class="section-title">Recent Sales</h4>
                <a href="<?php echo e(route('admin.sales.index')); ?>" class="text-sm font-semibold text-primary hover:underline">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="table-saas">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $recentSales ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="font-mono-data">#<?php echo e(str_pad($sale->id, 5, '0', STR_PAD_LEFT)); ?></td>
                                <td class="font-medium"><?php echo e($sale->customer->name ?? 'Walk-in Customer'); ?></td>
                                <td class="text-on-surface-variant"><?php echo e($sale->created_at->format('M d, g:i A')); ?></td>
                                <td class="font-semibold">₹<?php echo e(number_format($sale->total_amount, 2)); ?></td>
                                <td><span class="badge badge-success">Completed</span></td>
                                <td class="text-right">
                                    <a href="<?php echo e(route('admin.sales.show', $sale)); ?>" class="btn-icon ml-auto">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <span class="material-symbols-outlined text-[32px] opacity-40">receipt_long</span>
                                        No recent sales found.
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/pharmacy/dashboard.blade.php ENDPATH**/ ?>