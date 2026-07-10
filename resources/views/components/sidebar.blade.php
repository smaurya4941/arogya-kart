@php
    $user = auth()->user();
    $menuGroups = [];

    if ($user?->isAdmin()) {
        $menuGroups = [
            [
                'label' => 'Overview',
                'items' => [
                    ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'dashboard'],
                    ['label' => 'Products', 'route' => 'admin.products.index', 'active' => 'admin.products.*', 'icon' => 'inventory_2'],
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
@endphp

<!-- SideNavBar (Desktop) -->
<aside
    x-cloak
    class="fixed left-0 top-0 h-full w-[280px] bg-white/70 dark:bg-on-background/70 backdrop-blur-xl border-r border-outline-variant/30 dark:border-outline/20 shadow-sm z-50 flex flex-col py-6 transition-transform duration-300 lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    <div class="px-6 mb-8 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-on-primary shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined text-title-lg" style="font-variation-settings: 'FILL' 1;">medical_services</span>
            </div>
            <div>
                <a href="{{ url('/') }}">
                    <h1 class="font-headline-md text-headline-md font-bold text-primary dark:text-primary-fixed leading-tight">PharmaFlow</h1>
                    <p class="text-label-md font-label-md text-on-surface-variant/70 uppercase tracking-widest">Enterprise Suite</p>
                </a>
            </div>
        </div>
        
        <button
            type="button"
            class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-on-surface-variant hover:bg-surface-variant/20 lg:hidden"
            @click="sidebarOpen = false"
            aria-label="Close sidebar"
        >
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>

    <nav class="flex-1 px-4 space-y-4 overflow-y-auto custom-scrollbar">
        @foreach ($menuGroups as $group)
            <div class="space-y-1">
                @if(count($menuGroups) > 1)
                    <p class="px-4 text-[10px] font-bold uppercase tracking-widest text-outline-variant mb-2">
                        {{ $group['label'] }}
                    </p>
                @endif

                @foreach ($group['items'] as $item)
                    @php
                        $isAvailable = $item['available'] ?? true;
                        $isActive = $isAvailable && isset($item['active']) && request()->routeIs($item['active']);
                    @endphp

                    @if ($isAvailable)
                        <a
                            href="{{ route($item['route']) }}"
                            @click="sidebarOpen = false"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl {{ $isActive ? 'text-primary dark:text-primary-fixed-dim font-bold border-r-4 border-primary dark:border-primary-fixed-dim bg-primary/5' : 'text-on-surface-variant dark:text-outline-variant hover:bg-primary/10' }} active:scale-95 transition-all duration-150"
                        >
                            <span class="material-symbols-outlined {{ $isActive ? 'active-icon' : '' }}">{{ $item['icon'] ?? 'circle' }}</span>
                            <span class="font-body-md text-body-md">{{ $item['label'] }}</span>
                        </a>
                    @else
                        <div class="flex items-center justify-between px-4 py-3 rounded-xl text-outline-variant/60 cursor-not-allowed">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined">{{ $item['icon'] ?? 'circle' }}</span>
                                <span class="font-body-md text-body-md">{{ $item['label'] }}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-surface-container text-on-surface-variant">Soon</span>
                        </div>
                    @endif
                @endforeach
            </div>
        @endforeach
    </nav>

    <div class="mt-auto px-6 pt-6 border-t border-outline-variant/20">
        <button class="w-full bg-primary text-on-primary py-3 px-4 rounded-xl font-bold flex items-center justify-center gap-2 shadow-lg shadow-primary/20 hover:opacity-90 active:scale-95 transition-all">
            <span class="material-symbols-outlined">add_circle</span>
            New Prescription
        </button>
        <div class="mt-6 space-y-2">
            <a class="flex items-center gap-3 px-4 py-2 text-on-surface-variant hover:text-primary transition-colors" href="{{ route('profile.edit') }}">
                <span class="material-symbols-outlined">settings</span>
                <span class="text-body-md">Settings</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-error hover:opacity-80 transition-opacity">
                    <span class="material-symbols-outlined">logout</span>
                    <span class="text-body-md">Sign Out</span>
                </button>
            </form>
        </div>
    </div>
</aside>
