<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <?php
        $appName = config('app.name', 'ArogyaKart');
        $sectionTitle = trim($__env->yieldContent('title'));
        $pageTitle = $sectionTitle !== '' ? $sectionTitle.' | '.$appName : $appName;
    ?>

    <title><?php echo e($pageTitle); ?></title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=Geist+Mono:wght@100..900&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <style>
        body { background-color: #f8f9ff; }
        .glass-nav { backdrop-filter: blur(20px); }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        .active-icon { font-variation-settings: 'FILL' 1; }
        
        /* Custom scrollbar for data density */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #bcc9c6; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #6d7a77; }
        
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        }
        @keyframes ripple {
            to { transform: scale(4); opacity: 0; }
        }
        button, a { position: relative; overflow: hidden; }
    </style>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="font-body-md text-on-surface antialiased overflow-x-hidden">
    <?php if(auth()->guard()->check()): ?>
        <div x-data="{ sidebarOpen: false }" class="min-h-screen flex flex-col lg:flex-row">
            <!-- Mobile Sidebar Overlay -->
            <div
                x-cloak
                x-show="sidebarOpen"
                x-transition.opacity
                class="fixed inset-0 z-40 bg-on-background/40 lg:hidden"
                @click="sidebarOpen = false"
                aria-hidden="true"
            ></div>

            <?php if (isset($component)) { $__componentOriginal2880b66d47486b4bfeaf519598a469d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2880b66d47486b4bfeaf519598a469d6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sidebar','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2880b66d47486b4bfeaf519598a469d6)): ?>
<?php $attributes = $__attributesOriginal2880b66d47486b4bfeaf519598a469d6; ?>
<?php unset($__attributesOriginal2880b66d47486b4bfeaf519598a469d6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2880b66d47486b4bfeaf519598a469d6)): ?>
<?php $component = $__componentOriginal2880b66d47486b4bfeaf519598a469d6; ?>
<?php unset($__componentOriginal2880b66d47486b4bfeaf519598a469d6); ?>
<?php endif; ?>

            <div class="flex min-h-screen flex-1 flex-col lg:ml-[248px]">
                <?php if (isset($component)) { $__componentOriginala591787d01fe92c5706972626cdf7231 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala591787d01fe92c5706972626cdf7231 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.navbar','data' => ['title' => trim($__env->yieldContent('title')) ?: 'Dashboard','subtitle' => trim($__env->yieldContent('subtitle')) ?: null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('navbar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trim($__env->yieldContent('title')) ?: 'Dashboard'),'subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(trim($__env->yieldContent('subtitle')) ?: null)]); ?>
                    <?php if (! empty(trim($__env->yieldContent('actions')))): ?>
                        <?php echo $__env->yieldContent('actions'); ?>
                    <?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala591787d01fe92c5706972626cdf7231)): ?>
<?php $attributes = $__attributesOriginala591787d01fe92c5706972626cdf7231; ?>
<?php unset($__attributesOriginala591787d01fe92c5706972626cdf7231); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala591787d01fe92c5706972626cdf7231)): ?>
<?php $component = $__componentOriginala591787d01fe92c5706972626cdf7231; ?>
<?php unset($__componentOriginala591787d01fe92c5706972626cdf7231); ?>
<?php endif; ?>

                <?php if(session()->has(\App\Http\Controllers\SuperAdmin\ImpersonationController::SESSION_KEY)): ?>
                    <div class="bg-inverse-surface text-inverse-on-surface px-6 py-2.5 flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-base">visibility</span>
                            You are impersonating <strong><?php echo e(auth()->user()->pharmacy?->name ?? auth()->user()->name); ?></strong>. Actions you take affect this pharmacy.
                        </span>
                        <form method="POST" action="<?php echo e(route('impersonate.leave')); ?>">
                            <?php echo csrf_field(); ?>
                            <button class="bg-white/20 hover:bg-white/30 rounded-lg px-3 py-1 font-medium transition">Return to platform admin</button>
                        </form>
                    </div>
                <?php endif; ?>

                <?php
                    $liveAnnouncements = \App\Models\Announcement::cachedLive();
                    $announcementStyles = [
                        'info'     => 'bg-primary/10 text-primary',
                        'warning'  => 'bg-amber-100 text-amber-800',
                        'critical' => 'bg-error-container text-on-error-container',
                    ];
                    $announcementIcons = ['info' => 'campaign', 'warning' => 'warning', 'critical' => 'error'];
                ?>
                <?php $__currentLoopData = $liveAnnouncements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="px-6 py-2.5 text-sm <?php echo e($announcementStyles[$announcement->level] ?? $announcementStyles['info']); ?>">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-base"><?php echo e($announcementIcons[$announcement->level] ?? 'campaign'); ?></span>
                            <strong><?php echo e($announcement->title); ?></strong>
                            <span class="opacity-90"><?php echo e($announcement->body); ?></span>
                        </span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <main class="flex-1 px-4 py-5 pb-24 sm:px-6 lg:pb-8">
                    <?php if (isset($component)) { $__componentOriginalbb0843bd48625210e6e530f88101357e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbb0843bd48625210e6e530f88101357e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.flash-message','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flash-message'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbb0843bd48625210e6e530f88101357e)): ?>
<?php $attributes = $__attributesOriginalbb0843bd48625210e6e530f88101357e; ?>
<?php unset($__attributesOriginalbb0843bd48625210e6e530f88101357e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbb0843bd48625210e6e530f88101357e)): ?>
<?php $component = $__componentOriginalbb0843bd48625210e6e530f88101357e; ?>
<?php unset($__componentOriginalbb0843bd48625210e6e530f88101357e); ?>
<?php endif; ?>

                    <?php if (! empty(trim($__env->yieldContent('content')))): ?>
                        <?php echo $__env->yieldContent('content'); ?>
                    <?php else: ?>
                        <?php echo e($slot ?? ''); ?>

                    <?php endif; ?>
                </main>
                
                <!-- BottomNavBar (Mobile) — role-aware, only links to routes the user can reach -->
                <?php
                    $navUser = auth()->user();
                    $bottomNav = [
                        ['label' => 'Home', 'icon' => 'home', 'url' => route('dashboard'), 'match' => ['dashboard', '*.dashboard']],
                    ];
                    if (\Illuminate\Support\Facades\Route::has('admin.pos.index') && $navUser?->can('create', \App\Models\Sale::class)) {
                        $bottomNav[] = ['label' => 'POS', 'icon' => 'point_of_sale', 'url' => route('admin.pos.index'), 'match' => ['admin.pos.*']];
                    }
                    if (\Illuminate\Support\Facades\Route::has('admin.products.index') && $navUser?->can('viewAny', \App\Models\Product::class)) {
                        $bottomNav[] = ['label' => 'Stock', 'icon' => 'inventory_2', 'url' => route('admin.products.index'), 'match' => ['admin.products.*']];
                    }
                    $navUnread = $navUser?->unreadNotifications()->count() ?? 0;
                    if (\Illuminate\Support\Facades\Route::has('admin.notifications.index')) {
                        $bottomNav[] = ['label' => 'Alerts', 'icon' => 'notifications', 'url' => route('admin.notifications.index'), 'match' => ['admin.notifications.*'], 'badge' => $navUnread];
                    }
                ?>
                <nav class="fixed bottom-0 left-0 z-50 flex w-full items-center justify-around border-t border-outline-variant/40 bg-white/90 px-2 py-1.5 backdrop-blur-xl dark:bg-on-background lg:hidden">
                    <?php $__currentLoopData = $bottomNav; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $active = call_user_func_array([request(), 'routeIs'], $item['match']); ?>
                        <a href="<?php echo e($item['url']); ?>" class="relative flex flex-1 flex-col items-center justify-center gap-0.5 rounded-lg py-1.5 text-[10px] font-medium transition-colors <?php echo e($active ? 'text-primary' : 'text-on-surface-variant'); ?>">
                            <span class="material-symbols-outlined text-[22px] <?php echo e($active ? 'active-icon' : ''); ?>"><?php echo e($item['icon']); ?></span>
                            <span><?php echo e($item['label']); ?></span>
                            <?php if(($item['badge'] ?? 0) > 0): ?>
                                <span class="absolute right-1/4 top-1 h-2 w-2 rounded-full bg-error ring-2 ring-white"></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </nav>
            </div>
        </div>
    <?php else: ?>
        <div class="min-h-screen">
            <?php if (! empty(trim($__env->yieldContent('content')))): ?>
                <?php echo $__env->yieldContent('content'); ?>
            <?php else: ?>
                <?php echo e($slot ?? ''); ?>

            <?php endif; ?>
        </div>
    <?php endif; ?>

    <script>
        // Simple micro-interactions
        document.querySelectorAll('button, a').forEach(elem => {
            elem.addEventListener('click', function(e) {
                const ripple = document.createElement('div');
                ripple.classList.add('ripple');
                this.appendChild(ripple);
                setTimeout(() => ripple.remove(), 600);
            });
        });
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/layouts/app.blade.php ENDPATH**/ ?>