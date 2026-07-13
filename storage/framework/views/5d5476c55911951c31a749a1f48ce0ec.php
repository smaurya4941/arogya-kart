<?php
    $user = auth()->user();
    $brand = config('app.name', 'ArogyaKart');
    $menuGroups = [];

    if ($user?->isAdmin()) {
        $menuGroups = [
            [
                'label' => 'Overview',
                'items' => [
                    ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'dashboard'],
                    ['label' => 'Products', 'route' => 'admin.products.index', 'active' => 'admin.products.*', 'icon' => 'inventory_2'],
                    ['label' => 'Categories', 'route' => 'admin.categories.index', 'active' => 'admin.categories.*', 'icon' => 'category'],
                ],
            ],
            [
                'label' => 'Operations',
                'items' => [
                    ['label' => 'Billing / POS', 'route' => 'admin.pos.index', 'active' => 'admin.pos.*', 'icon' => 'point_of_sale'],
                    ['label' => 'Sales', 'route' => 'admin.sales.index', 'active' => 'admin.sales.*', 'icon' => 'receipt_long'],
                    ['label' => 'Returns', 'route' => 'admin.returns.index', 'active' => 'admin.returns.*', 'icon' => 'assignment_return'],
                    ['label' => 'Purchases', 'route' => 'admin.purchases.index', 'active' => 'admin.purchases.*', 'icon' => 'shopping_cart_checkout'],
                ],
            ],
            [
                'label' => 'People',
                'items' => [
                    ['label' => 'Customers', 'route' => 'admin.customers.index', 'active' => 'admin.customers.*', 'icon' => 'groups'],
                    ['label' => 'Suppliers', 'route' => 'admin.suppliers.index', 'active' => 'admin.suppliers.*', 'icon' => 'local_shipping'],
                ],
            ],
            [
                'label' => 'Reports',
                'items' => [
                    ['label' => 'Sales Report', 'route' => 'admin.reports.sales', 'active' => 'admin.reports.sales', 'icon' => 'monitoring'],
                    ['label' => 'Profit & Loss', 'route' => 'admin.reports.profit', 'active' => 'admin.reports.profit', 'icon' => 'account_balance_wallet'],
                    ['label' => 'GST Report', 'route' => 'admin.reports.gst', 'active' => 'admin.reports.gst', 'icon' => 'request_quote'],
                ],
            ],
            [
                'label' => 'Management',
                'items' => [
                    ['label' => 'Team', 'route' => 'admin.team.index', 'active' => 'admin.team.*', 'icon' => 'badge'],
                    ['label' => 'Subscription', 'route' => 'admin.subscription.index', 'active' => 'admin.subscription.*', 'icon' => 'workspace_premium'],
                ],
            ],
        ];
    } elseif ($user?->isStaff()) {
        // Staff see only the tools their position (Spatie role) grants. Each item
        // is gated by the same policy the route enforces, so the menu never offers
        // a link that would 403.
        $staffItems = [
            ['label' => 'Dashboard', 'route' => 'staff.dashboard', 'active' => 'staff.dashboard', 'icon' => 'dashboard'],
        ];

        if ($user->can('create', \App\Models\Sale::class)) {
            $staffItems[] = ['label' => 'Billing / POS', 'route' => 'admin.pos.index', 'active' => 'admin.pos.*', 'icon' => 'point_of_sale'];
        }
        if ($user->can('viewAny', \App\Models\Sale::class)) {
            $staffItems[] = ['label' => 'Sales', 'route' => 'admin.sales.index', 'active' => 'admin.sales.*', 'icon' => 'receipt_long'];
        }
        if ($user->can('create', \App\Models\SaleReturn::class)) {
            $staffItems[] = ['label' => 'Returns', 'route' => 'admin.returns.index', 'active' => 'admin.returns.*', 'icon' => 'assignment_return'];
        }
        if ($user->can('viewAny', \App\Models\Product::class)) {
            $staffItems[] = ['label' => 'Inventory', 'route' => 'admin.products.index', 'active' => 'admin.products.*', 'icon' => 'inventory_2'];
        }

        $menuGroups = [
            ['label' => 'Staff', 'items' => $staffItems],
        ];
    } elseif ($user?->isClient()) {
        $menuGroups = [
            [
                'label' => 'Client',
                'items' => [
                    ['label' => 'Dashboard', 'route' => 'client.dashboard', 'active' => 'client.dashboard', 'icon' => 'dashboard'],
                ],
            ],
        ];
    }
?>

<!-- SideNavBar (Desktop) -->
<aside
    x-cloak
    class="fixed left-0 top-0 z-50 flex h-full w-[248px] flex-col border-r border-outline-variant/40 bg-white/80 py-4 shadow-sm backdrop-blur-xl transition-transform duration-300 dark:border-outline/20 dark:bg-on-background/70 lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    <div class="mb-5 flex items-center justify-between px-5">
        <a href="<?php echo e(url('/')); ?>" class="flex items-center gap-2.5">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary text-on-primary shadow-sm">
                <span class="material-symbols-outlined text-[22px]" style="font-variation-settings: 'FILL' 1;">medical_services</span>
            </div>
            <div class="leading-tight">
                <h1 class="text-base font-bold tracking-tight text-on-surface dark:text-primary-fixed"><?php echo e($brand); ?></h1>
                <p class="text-[10px] font-medium uppercase tracking-widest text-on-surface-variant/70">Pharmacy OS</p>
            </div>
        </a>

        <button
            type="button"
            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-on-surface-variant hover:bg-surface-container-low lg:hidden"
            @click="sidebarOpen = false"
            aria-label="Close sidebar"
        >
            <span class="material-symbols-outlined text-[20px]">close</span>
        </button>
    </div>

    <nav class="custom-scrollbar flex-1 space-y-4 overflow-y-auto px-3">
        <?php $__currentLoopData = $menuGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="space-y-0.5">
                <?php if(count($menuGroups) > 1): ?>
                    <p class="px-3 pb-1 text-[10px] font-bold uppercase tracking-widest text-outline-variant">
                        <?php echo e($group['label']); ?>

                    </p>
                <?php endif; ?>

                <?php $__currentLoopData = $group['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $isAvailable = $item['available'] ?? true;
                        $isActive = $isAvailable && isset($item['active']) && request()->routeIs($item['active']);
                    ?>

                    <?php if($isAvailable): ?>
                        <a
                            href="<?php echo e(route($item['route'])); ?>"
                            @click="sidebarOpen = false"
                            class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors <?php echo e($isActive ? 'bg-primary/10 font-semibold text-primary dark:text-primary-fixed-dim' : 'font-medium text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface dark:text-outline-variant'); ?>"
                        >
                            <span class="material-symbols-outlined text-[20px] <?php echo e($isActive ? 'active-icon' : ''); ?>"><?php echo e($item['icon'] ?? 'circle'); ?></span>
                            <span><?php echo e($item['label']); ?></span>
                        </a>
                    <?php else: ?>
                        <div class="flex items-center justify-between rounded-lg px-3 py-2 text-sm text-outline-variant/60">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-[20px]"><?php echo e($item['icon'] ?? 'circle'); ?></span>
                                <span class="font-medium"><?php echo e($item['label']); ?></span>
                            </div>
                            <span class="badge badge-neutral">Soon</span>
                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </nav>

    <div class="mt-auto space-y-1 border-t border-outline-variant/30 px-3 pt-3">
        <a class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-on-surface-variant transition-colors hover:bg-surface-container-low hover:text-on-surface" href="<?php echo e(route('profile.edit')); ?>">
            <span class="material-symbols-outlined text-[20px]">settings</span>
            <span>Settings</span>
        </a>
        <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-error transition-colors hover:bg-error/10">
                <span class="material-symbols-outlined text-[20px]">logout</span>
                <span>Sign Out</span>
            </button>
        </form>
    </div>
</aside>
<?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/components/sidebar.blade.php ENDPATH**/ ?>