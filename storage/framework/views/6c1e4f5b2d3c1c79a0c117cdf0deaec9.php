<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Platform Admin'); ?> | <?php echo e(config('app.name')); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>.material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400;vertical-align:middle;}</style>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="bg-background text-on-surface antialiased">
<div x-data="{ open: false }" class="flex min-h-screen">
    
    <aside class="fixed inset-y-0 hidden w-60 flex-col bg-on-background text-outline-variant lg:flex">
        <div class="flex h-14 items-center gap-2 border-b border-white/10 px-5">
            <span class="text-base font-bold text-white"><?php echo e(config('app.name')); ?></span>
            <span class="rounded bg-primary px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-widest text-on-primary">Platform</span>
        </div>
        <nav class="flex-1 space-y-1 px-3 py-4 text-sm">
            <?php
                // Each row: [route, label, icon, capability]. A null capability is
                // always shown; otherwise the item appears only if the current
                // super admin holds that capability.
                $nav = [
                    ['superadmin.dashboard', 'Dashboard', 'dashboard', null],
                    ['superadmin.pharmacies.index', 'Pharmacies', 'local_pharmacy', \App\Support\AdminCapability::PHARMACIES],
                    ['superadmin.operations.index', 'Operations', 'inventory_2', \App\Support\AdminCapability::OPERATIONS],
                    ['superadmin.users.index', 'Users', 'group', \App\Support\AdminCapability::USERS],
                    ['superadmin.subscriptions.index', 'Subscriptions', 'card_membership', \App\Support\AdminCapability::BILLING],
                    ['superadmin.invoices.index', 'Invoices', 'receipt_long', \App\Support\AdminCapability::BILLING],
                    ['superadmin.plans.index', 'Plans', 'workspace_premium', \App\Support\AdminCapability::BILLING],
                    ['superadmin.coupons.index', 'Coupons', 'sell', \App\Support\AdminCapability::BILLING],
                    ['superadmin.announcements.index', 'Announcements', 'campaign', \App\Support\AdminCapability::ANNOUNCEMENTS],
                    ['superadmin.audit.index', 'Activity Log', 'history', \App\Support\AdminCapability::AUDIT],
                    ['superadmin.system.index', 'System Health', 'monitor_heart', \App\Support\AdminCapability::SYSTEM],
                    ['superadmin.settings.index', 'Settings', 'settings', \App\Support\AdminCapability::SETTINGS],
                ];
            ?>
            <?php $__currentLoopData = $nav; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$route, $label, $icon, $capability]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($capability !== null && ! auth()->user()->hasAdminCapability($capability)) continue; ?>
                <?php $active = request()->routeIs(Str::before($route, '.index').'*') || request()->routeIs($route); ?>
                <a href="<?php echo e(route($route)); ?>"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 font-medium transition <?php echo e($active ? 'bg-white/10 text-white' : 'text-outline-variant hover:bg-white/5 hover:text-white'); ?>">
                    <span class="material-symbols-outlined text-[20px]"><?php echo e($icon); ?></span>
                    <?php echo e($label); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </nav>
        <div class="border-t border-white/10 p-3">
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm text-outline-variant transition hover:bg-white/5 hover:text-white">
                    <span class="material-symbols-outlined text-[20px]">logout</span> Sign out
                </button>
            </form>
        </div>
    </aside>

    
    <div class="flex min-h-screen flex-1 flex-col lg:ml-60">
        <header class="sticky top-0 z-30 flex h-14 items-center justify-between border-b border-outline-variant/40 bg-white/80 px-6 backdrop-blur-xl">
            <h1 class="text-base font-bold text-on-surface"><?php echo $__env->yieldContent('title', 'Platform Admin'); ?></h1>
            <div class="text-sm text-on-surface-variant"><?php echo e(auth()->user()->name); ?></div>
        </header>

        <main class="flex-1 p-4 sm:p-6">
            <?php if(session('success')): ?>
                <div class="mb-4 rounded-lg border border-tertiary/30 bg-tertiary-container/15 px-4 py-3 text-sm text-tertiary"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="mb-4 rounded-lg border border-error/30 bg-error-container/40 px-4 py-3 text-sm text-on-error-container"><?php echo e(session('error')); ?></div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
                <div class="mb-4 rounded-lg border border-error/30 bg-error-container/40 px-4 py-3 text-sm text-on-error-container">
                    <ul class="list-inside list-disc">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>
</div>
</body>
</html>
<?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/layouts/superadmin.blade.php ENDPATH**/ ?>