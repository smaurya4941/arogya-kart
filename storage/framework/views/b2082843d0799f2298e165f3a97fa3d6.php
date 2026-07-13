<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => 'Dashboard',
    'subtitle' => null,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'title' => 'Dashboard',
    'subtitle' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $user = auth()->user();
    $unread = $user?->unreadNotifications()->count() ?? 0;
    $notifRoute = \Illuminate\Support\Facades\Route::has('admin.notifications.index')
        ? route('admin.notifications.index')
        : null;
?>

<!-- TopNavBar -->
<header class="sticky top-0 z-30 flex h-14 items-center justify-between border-b border-outline-variant/40 bg-white/80 px-4 backdrop-blur-xl dark:border-outline/20 dark:bg-on-background/70 sm:px-6">
    <div class="flex min-w-0 items-center gap-3">
        <!-- Mobile Menu Toggle -->
        <button
            type="button"
            class="btn-icon lg:hidden"
            @click="sidebarOpen = true"
            aria-label="Open sidebar"
        >
            <span class="material-symbols-outlined text-[22px]">menu</span>
        </button>

        <!-- Page title (desktop) -->
        <div class="hidden min-w-0 lg:block">
            <h1 class="truncate text-base font-bold leading-tight text-on-surface"><?php echo e($title); ?></h1>
            <?php if($subtitle): ?>
                <p class="truncate text-xs text-on-surface-variant"><?php echo e($subtitle); ?></p>
            <?php endif; ?>
        </div>

        <!-- Search (desktop) -->
        <div class="relative ml-2 hidden xl:block">
            <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-[20px] text-outline">search</span>
            <input class="h-9 w-64 rounded-lg border border-outline-variant/40 bg-surface-container-low pl-9 pr-3 text-sm placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary/20" placeholder="Search…" type="text"/>
        </div>

        <!-- Mobile Page Title -->
        <div class="min-w-0 lg:hidden">
            <h1 class="truncate text-base font-bold leading-tight text-on-surface"><?php echo e($title); ?></h1>
        </div>
    </div>

    <div class="flex items-center gap-1.5 sm:gap-2">
        <?php if(trim($slot)): ?>
            <div class="mr-1 hidden items-center gap-2 md:flex"><?php echo e($slot); ?></div>
        <?php endif; ?>

        <?php if($notifRoute): ?>
            <a href="<?php echo e($notifRoute); ?>" class="btn-icon relative">
                <span class="material-symbols-outlined text-[22px]">notifications</span>
                <?php if($unread > 0): ?>
                    <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-error ring-2 ring-white"></span>
                <?php endif; ?>
            </a>
        <?php endif; ?>

        <a href="<?php echo e(route('profile.edit')); ?>" class="flex items-center gap-2 rounded-full border border-outline-variant/40 bg-white p-0.5 pr-2.5 transition-colors hover:bg-surface-container-low">
            <div class="flex h-7 w-7 items-center justify-center rounded-full bg-primary text-xs font-bold text-on-primary">
                <?php echo e(strtoupper(substr($user?->name ?? 'U', 0, 1))); ?>

            </div>
            <div class="hidden flex-col text-left leading-none sm:flex">
                <span class="text-xs font-semibold text-on-surface"><?php echo e($user?->name); ?></span>
                <span class="text-[10px] uppercase tracking-wider text-on-surface-variant"><?php echo e($user?->role?->value ?? 'user'); ?></span>
            </div>
        </a>
    </div>
</header>
<?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/components/navbar.blade.php ENDPATH**/ ?>