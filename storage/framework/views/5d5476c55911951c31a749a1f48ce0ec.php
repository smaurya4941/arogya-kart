<?php
    $user = auth()->user();
    $menuGroups = [];

    if ($user?->isAdmin()) {
        $menuGroups = [
            [
                'label' => 'Overview',
                'items' => [
                    ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard'],
                    ['label' => 'Products', 'route' => 'admin.products.index', 'active' => 'admin.products.*'],
                    ['label' => 'Profile', 'route' => 'profile.edit', 'active' => 'profile.*'],
                ],
            ],
            [
                'label' => 'Inventory',
                'items' => [
                    ['label' => 'Batch Tracking', 'route' => 'admin.products.index', 'active' => 'admin.products.*'],
                    ['label' => 'Expiry Alerts', 'route' => 'admin.products.index', 'active' => 'admin.products.*'],
                    ['label' => 'Low Stock Monitor', 'route' => 'admin.products.index', 'active' => 'admin.products.*'],
                ],
            ],
            [
                'label' => 'Purchasing',
                'items' => [
                    ['label' => 'Suppliers', 'route' => 'admin.suppliers.index', 'active' => 'admin.suppliers.*'],
                    ['label' => 'Purchases', 'route' => 'admin.purchases.index', 'active' => 'admin.purchases.*'],
                ],
            ],
            [
                'label' => 'Operations',
                'items' => [
                    ['label' => 'Billing / POS', 'route' => 'admin.pos.index', 'active' => 'admin.pos.*'],
                    ['label' => 'Sales', 'route' => 'admin.sales.index', 'active' => 'admin.sales.*'],
                    ['label' => 'Customers', 'route' => 'admin.customers.index', 'active' => 'admin.customers.*'],
                ],
            ],
            [
                'label' => 'Accounting',
                'items' => [
                    ['label' => 'Expenses', 'route' => 'admin.expenses.index', 'active' => 'admin.expenses.*'],
                    ['label' => 'Sales Report', 'route' => 'admin.reports.sales', 'active' => 'admin.reports.sales'],
                    ['label' => 'Purchase Report', 'route' => 'admin.reports.purchases', 'active' => 'admin.reports.purchases'],
                    ['label' => 'Profit & Loss', 'route' => 'admin.reports.profit', 'active' => 'admin.reports.profit'],
                    ['label' => 'GST Report', 'route' => 'admin.reports.gst', 'active' => 'admin.reports.gst'],
                    ['label' => 'Inventory Valuation', 'route' => 'admin.reports.inventory', 'active' => 'admin.reports.inventory'],
                ],
            ],
        ];
    } elseif ($user?->isStaff()) {
        $menuGroups = [
            [
                'label' => 'Staff',
                'items' => [
                    ['label' => 'Dashboard', 'route' => 'staff.dashboard', 'active' => 'staff.dashboard'],
                    ['label' => 'Profile', 'route' => 'profile.edit', 'active' => 'profile.*'],
                    ['label' => 'Billing / POS', 'available' => false],
                ],
            ],
        ];
    } elseif ($user?->isClient()) {
        $menuGroups = [
            [
                'label' => 'Client',
                'items' => [
                    ['label' => 'Dashboard', 'route' => 'client.dashboard', 'active' => 'client.dashboard'],
                    ['label' => 'Profile', 'route' => 'profile.edit', 'active' => 'profile.*'],
                    ['label' => 'My Orders', 'available' => false],
                ],
            ],
        ];
    }
?>

<aside
    x-cloak
    class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col border-r border-emerald-800 bg-primary text-white shadow-2xl transition-transform duration-300 lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    <div class="flex items-center justify-between border-b border-white/10 px-6 py-5">
        <div>
            <a href="<?php echo e(url('/')); ?>" class="text-2xl font-bold tracking-tight">ArogyaKart</a>
            <p class="mt-1 text-xs uppercase tracking-[0.3em] text-emerald-100/80">Pharmacy SaaS</p>
        </div>

        <button
            type="button"
            class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-white/15 text-white lg:hidden"
            @click="sidebarOpen = false"
            aria-label="Close sidebar"
        >
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>

    <div class="border-b border-white/10 px-6 py-4">
        <p class="text-sm font-semibold"><?php echo e($user?->name); ?></p>
        <p class="mt-1 text-xs uppercase tracking-[0.25em] text-emerald-100/75"><?php echo e($user?->role instanceof \BackedEnum ? $user->role->value : ($user?->role ?? 'guest')); ?></p>
    </div>

    <nav class="flex-1 space-y-6 overflow-y-auto px-4 py-6">
        <?php $__currentLoopData = $menuGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="space-y-2">
                <p class="px-3 text-xs font-semibold uppercase tracking-[0.25em] text-emerald-100/70">
                    <?php echo e($group['label']); ?>

                </p>

                <?php $__currentLoopData = $group['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $isAvailable = $item['available'] ?? true;
                        $isActive = $isAvailable && isset($item['active']) && request()->routeIs($item['active']);
                        $baseClasses = 'flex items-center justify-between rounded-2xl px-3 py-2.5 text-sm font-medium transition';
                    ?>

                    <?php if($isAvailable): ?>
                        <a
                            href="<?php echo e(route($item['route'])); ?>"
                            @click="sidebarOpen = false"
                            class="<?php echo e($baseClasses); ?> <?php echo e($isActive ? 'bg-white text-primary shadow-lg shadow-emerald-950/15' : 'text-white/90 hover:bg-white/10 hover:text-white'); ?>"
                        >
                            <span><?php echo e($item['label']); ?></span>
                            <?php if($isActive): ?>
                                <span class="h-2.5 w-2.5 rounded-full bg-primary"></span>
                            <?php endif; ?>
                        </a>
                    <?php else: ?>
                        <div class="<?php echo e($baseClasses); ?> cursor-not-allowed text-white/45">
                            <span><?php echo e($item['label']); ?></span>
                            <span class="rounded-full border border-white/10 px-2 py-0.5 text-[10px] uppercase tracking-[0.2em]">
                                Soon
                            </span>
                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </nav>
</aside>
<?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/components/sidebar.blade.php ENDPATH**/ ?>