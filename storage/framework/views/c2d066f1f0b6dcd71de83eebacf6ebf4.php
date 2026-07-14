<!DOCTYPE html>
<html class="light" lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo e(config('app.name', 'ArogyaKart')); ?> | Modern Pharmacy Management SaaS</title>
<!-- Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&amp;family=JetBrains+Mono&amp;family=Geist:wght@400;600;800&amp;display=swap" rel="stylesheet"/>
<!-- Material Symbols -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20;
            font-size: 20px;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
            border: 1px solid #bcc9c6;
        }
        .dashboard-grid {
            background-image: radial-gradient(#bcc9c6 1px, transparent 1px);
            background-size: 20px 20px;
        }
        body {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        html { scroll-behavior: smooth; }
    </style>
<script id="tailwind-config">
        tailwind.config = {
          darkMode: "class",
          theme: {
            extend: {
              // Current ArogyaKart brand palette (kept as-is; only the landing structure is new).
              "colors": {
                "primary": "#00685f",
                "on-primary": "#ffffff",
                "primary-container": "#008378",
                "on-primary-container": "#f4fffc",
                "primary-fixed": "#89f5e7",
                "primary-fixed-dim": "#6bd8cb",
                "secondary": "#006398",
                "on-secondary": "#ffffff",
                "secondary-container": "#5bb8fe",
                "secondary-fixed": "#cce5ff",
                "secondary-fixed-dim": "#93ccff",
                "tertiary": "#006b2d",
                "on-tertiary": "#ffffff",
                "tertiary-container": "#00873b",
                "on-tertiary-container": "#f7fff3",
                "tertiary-fixed": "#6bff8f",
                "tertiary-fixed-dim": "#4ae176",
                "background": "#f8f9ff",
                "on-background": "#0b1c30",
                "surface": "#f8f9ff",
                "surface-bright": "#f8f9ff",
                "surface-dim": "#cbdbf5",
                "surface-variant": "#d3e4fe",
                "on-surface": "#0b1c30",
                "on-surface-variant": "#3d4947",
                "outline": "#6d7a77",
                "outline-variant": "#bcc9c6",
                "surface-container-lowest": "#ffffff",
                "surface-container-low": "#eff4ff",
                "surface-container": "#e5eeff",
                "surface-container-high": "#dce9ff",
                "surface-container-highest": "#d3e4fe",
                "inverse-surface": "#213145",
                "inverse-on-surface": "#eaf1ff",
                "inverse-primary": "#6bd8cb",
                "error": "#ba1a1a",
                "on-error": "#ffffff",
                "error-container": "#ffdad6",
                "on-error-container": "#93000a"
              },
              "borderRadius": {
                "DEFAULT": "0.125rem",
                "lg": "0.25rem",
                "xl": "0.5rem",
                "full": "0.75rem"
              },
              "spacing": {
                "base": "4px",
                "gutter": "16px",
                "xs": "4px",
                "sm": "8px",
                "xl": "40px",
                "md": "16px",
                "lg": "24px",
                "container-max": "1440px"
              },
              "fontFamily": {
                "body-lg": ["Inter"],
                "code-sm": ["JetBrains Mono"],
                "headline-lg": ["Geist"],
                "display": ["Geist"],
                "headline-md": ["Geist"],
                "body-md": ["Inter"],
                "body-sm": ["Inter"],
                "label-md": ["Inter"]
              },
              "fontSize": {
                "body-lg": ["15px", {"lineHeight": "22px", "fontWeight": "400"}],
                "code-sm": ["11px", {"lineHeight": "16px", "fontWeight": "400"}],
                "headline-lg": ["24px", {"lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                "display": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.02em", "fontWeight": "600"}],
                "headline-md": ["18px", {"lineHeight": "24px", "fontWeight": "600"}],
                "body-md": ["13px", {"lineHeight": "20px", "fontWeight": "400"}],
                "body-sm": ["12px", {"lineHeight": "18px", "fontWeight": "400"}],
                "label-md": ["12px", {"lineHeight": "16px", "fontWeight": "600"}]
              }
            },
          },
        }
    </script>
</head>
<body class="bg-background text-on-surface font-body-md text-body-md">
<?php
    $ctaUrl = auth()->check() ? route('dashboard') : route('register');
    $ctaLabel = auth()->check() ? 'Go to Dashboard' : 'Get Started';
?>
<!-- TopNavBar -->
<header class="bg-surface top-0 sticky z-50 border-b border-outline-variant">
<nav class="flex justify-between items-center px-lg py-sm max-w-container-max mx-auto">
<div class="flex items-center gap-md">
<a href="<?php echo e(url('/')); ?>" class="flex items-center gap-sm font-headline-md text-headline-md font-bold text-primary">
<span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">medical_services</span>
<?php echo e(config('app.name', 'ArogyaKart')); ?>

</a>
</div>
<div class="hidden md:flex items-center gap-lg">
<a class="text-on-surface-variant hover:text-primary transition-colors" href="#features">Features</a>
<a class="text-on-surface-variant hover:text-primary transition-colors" href="#solutions">Solutions</a>
<a class="text-on-surface-variant hover:text-primary transition-colors" href="#pricing">Pricing</a>
<a class="text-on-surface-variant hover:text-primary transition-colors" href="#contact">Contact</a>
</div>
<div class="flex items-center gap-sm">
<div class="hidden md:flex items-center gap-sm">
<?php if(auth()->guard()->check()): ?>
<a href="<?php echo e(route('dashboard')); ?>" class="bg-primary text-on-primary px-md py-xs rounded font-label-md text-label-md shadow-sm hover:bg-primary-container transition-colors">Dashboard</a>
<?php else: ?>
<a href="<?php echo e(route('login')); ?>" class="px-md py-xs font-label-md text-label-md text-primary hover:opacity-80 transition-opacity">Login</a>
<a href="<?php echo e(route('register')); ?>" class="bg-primary text-on-primary px-md py-xs rounded font-label-md text-label-md shadow-sm hover:bg-primary-container transition-colors">Get Started</a>
<?php endif; ?>
</div>
<!-- Mobile menu toggle -->
<button type="button" id="nav-toggle" aria-label="Toggle menu" aria-expanded="false" class="md:hidden inline-flex items-center justify-center h-9 w-9 rounded text-on-surface-variant hover:bg-surface-container transition-colors">
<span class="material-symbols-outlined" id="nav-toggle-icon">menu</span>
</button>
</div>
</nav>
<!-- Mobile menu panel -->
<div id="mobile-menu" class="hidden md:hidden border-t border-outline-variant bg-surface">
<div class="flex flex-col px-lg py-sm gap-1">
<a class="py-sm text-on-surface-variant hover:text-primary transition-colors" href="#features">Features</a>
<a class="py-sm text-on-surface-variant hover:text-primary transition-colors" href="#solutions">Solutions</a>
<a class="py-sm text-on-surface-variant hover:text-primary transition-colors" href="#pricing">Pricing</a>
<a class="py-sm text-on-surface-variant hover:text-primary transition-colors" href="#contact">Contact</a>
<div class="flex items-center gap-sm pt-sm mt-xs border-t border-outline-variant">
<?php if(auth()->guard()->check()): ?>
<a href="<?php echo e(route('dashboard')); ?>" class="flex-1 text-center bg-primary text-on-primary px-md py-sm rounded font-label-md text-label-md shadow-sm hover:bg-primary-container transition-colors">Dashboard</a>
<?php else: ?>
<a href="<?php echo e(route('login')); ?>" class="flex-1 text-center border border-outline-variant px-md py-sm rounded font-label-md text-label-md text-primary hover:bg-surface-container transition-colors">Login</a>
<a href="<?php echo e(route('register')); ?>" class="flex-1 text-center bg-primary text-on-primary px-md py-sm rounded font-label-md text-label-md shadow-sm hover:bg-primary-container transition-colors">Get Started</a>
<?php endif; ?>
</div>
</div>
</div>
</header>
<main>
<!-- Hero Section -->
<section class="relative pt-xl pb-24 overflow-hidden border-b border-outline-variant">
<div class="max-w-container-max mx-auto px-lg relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-xl items-center">
<div class="flex flex-col gap-md">
<span class="text-primary font-label-md tracking-wider">ENTERPRISE PHARMACY OS</span>
<h1 class="font-display text-display text-on-surface leading-tight">Modernizing Pharmacy Management.</h1>
<p class="text-on-surface-variant text-body-lg max-w-md">Cloud-based SaaS for inventory, sales, and AI-driven insights. Built for precision and scale.</p>
<div class="flex items-center gap-md mt-sm">
<a href="<?php echo e($ctaUrl); ?>" class="bg-primary text-on-primary h-8 px-md flex items-center justify-center font-label-md rounded shadow-md hover:brightness-110 transition-all"><?php echo e(auth()->check() ? 'Open Dashboard' : 'Start Free Trial'); ?></a>
<a href="#pricing" class="border border-outline-variant text-on-surface h-8 px-md flex items-center justify-center font-label-md rounded hover:bg-surface-container transition-all">Book Demo</a>
</div>
<div class="flex items-center gap-sm mt-md">
<span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">verified</span>
<span class="text-body-sm text-on-surface-variant">HIPAA &amp; GDPR Compliant Security Architecture</span>
</div>
</div>
<!-- UI Dashboard Mockup -->
<div class="relative">
<div class="glass-panel rounded-xl shadow-2xl p-sm overflow-hidden border border-outline-variant">
<div class="bg-surface-container-low h-6 w-full flex items-center px-sm gap-1 border-b border-outline-variant">
<div class="w-2 h-2 rounded-full bg-error opacity-50"></div>
<div class="w-2 h-2 rounded-full bg-tertiary-container opacity-50"></div>
<div class="w-2 h-2 rounded-full bg-secondary opacity-50"></div>
<div class="ml-4 h-3 w-48 bg-outline-variant rounded-full opacity-30"></div>
</div>
<div class="grid grid-cols-4 h-80 dashboard-grid">
<!-- Sidebar Mockup -->
<div class="col-span-1 border-r border-outline-variant p-sm flex flex-col gap-xs">
<div class="h-4 w-full bg-surface-container-highest rounded mb-sm"></div>
<div class="h-4 w-10/12 bg-primary-container opacity-20 rounded"></div>
<div class="h-4 w-9/12 bg-outline-variant opacity-10 rounded"></div>
<div class="h-4 w-11/12 bg-outline-variant opacity-10 rounded"></div>
<div class="mt-auto h-4 w-8/12 bg-outline-variant opacity-10 rounded"></div>
</div>
<!-- Content Mockup -->
<div class="col-span-3 p-md flex flex-col gap-md">
<div class="flex justify-between items-end">
<div class="flex flex-col gap-1">
<div class="h-2 w-16 bg-outline-variant opacity-40 rounded"></div>
<div class="h-6 w-32 bg-on-surface opacity-80 rounded"></div>
</div>
<div class="h-8 w-24 bg-primary rounded"></div>
</div>
<div class="grid grid-cols-3 gap-sm">
<div class="h-20 bg-white border border-outline-variant rounded p-xs flex flex-col justify-between">
<div class="h-2 w-10 bg-error opacity-20 rounded"></div>
<div class="h-6 w-14 bg-error opacity-80 rounded"></div>
</div>
<div class="h-20 bg-white border border-outline-variant rounded p-xs flex flex-col justify-between">
<div class="h-2 w-10 bg-secondary opacity-20 rounded"></div>
<div class="h-6 w-14 bg-secondary opacity-80 rounded"></div>
</div>
<div class="h-20 bg-white border border-outline-variant rounded p-xs flex flex-col justify-between">
<div class="h-2 w-10 bg-primary opacity-20 rounded"></div>
<div class="h-6 w-14 bg-primary opacity-80 rounded"></div>
</div>
</div>
<div class="mt-xs h-24 w-full border border-outline-variant rounded bg-white p-sm overflow-hidden">
<div class="flex items-end h-full gap-1">
<div class="flex-1 bg-primary-container h-[40%]"></div>
<div class="flex-1 bg-primary-container h-[60%]"></div>
<div class="flex-1 bg-primary-container h-[45%]"></div>
<div class="flex-1 bg-primary-container h-[80%]"></div>
<div class="flex-1 bg-primary-container h-[65%]"></div>
<div class="flex-1 bg-primary-container h-[90%]"></div>
<div class="flex-1 bg-primary-container h-[70%]"></div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Stats Bar -->
<section class="bg-surface-container-low border-b border-outline-variant py-md">
<div class="max-w-container-max mx-auto px-lg flex flex-wrap justify-between items-center gap-md">
<div class="flex items-center gap-md">
<span class="text-headline-lg font-display text-primary"><?php echo e($activePharmaciesCountStr ?: '0'); ?></span>
<span class="text-body-sm text-on-surface-variant font-label-md uppercase tracking-wider">Active Pharmacies</span>
</div>
<div class="w-px h-8 bg-outline-variant hidden md:block"></div>
<div class="flex items-center gap-md">
<span class="text-headline-lg font-display text-primary"><?php echo e($invoicesCountStr ?: '0'); ?></span>
<span class="text-body-sm text-on-surface-variant font-label-md uppercase tracking-wider">Processed Invoices</span>
</div>
<div class="w-px h-8 bg-outline-variant hidden md:block"></div>
<div class="flex items-center gap-md">
<span class="text-headline-lg font-display text-primary">99.9%</span>
<span class="text-body-sm text-on-surface-variant font-label-md uppercase tracking-wider">Uptime SLA</span>
</div>
<div class="w-px h-8 bg-outline-variant hidden md:block"></div>
<div class="flex items-center gap-md">
<span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">security</span>
<span class="text-body-sm text-on-surface-variant font-label-md uppercase tracking-wider">Tier-4 Datacenters</span>
</div>
</div>
</section>
<!-- Core Modules Grid -->
<section id="features" class="py-xl bg-white scroll-mt-16">
<div class="max-w-container-max mx-auto px-lg">
<div class="flex flex-col gap-xs mb-xl">
<h2 class="font-headline-lg text-headline-lg text-on-surface">Integrated Pharmacy Operations</h2>
<p class="text-on-surface-variant text-body-md max-w-xl">Every tool your pharmacy needs to operate at peak efficiency, consolidated into a single high-performance interface.</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-lg">
<!-- Inventory -->
<div class="p-lg border border-outline-variant hover:border-primary transition-all group">
<div class="w-10 h-10 rounded bg-primary-fixed flex items-center justify-center mb-md group-hover:bg-primary group-hover:text-white transition-colors">
<span class="material-symbols-outlined" data-icon="inventory_2">inventory_2</span>
</div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-xs">Inventory &amp; Batch</h3>
<p class="text-on-surface-variant text-body-sm leading-relaxed">Real-time stock tracking with automated expiry alerts and batch-level reconciliation.</p>
</div>
<!-- Sales -->
<div class="p-lg border border-outline-variant hover:border-primary transition-all group">
<div class="w-10 h-10 rounded bg-secondary-fixed flex items-center justify-center mb-md group-hover:bg-secondary group-hover:text-white transition-colors">
<span class="material-symbols-outlined" data-icon="point_of_sale">point_of_sale</span>
</div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-xs">Sales &amp; Billing</h3>
<p class="text-on-surface-variant text-body-sm leading-relaxed">Lightning-fast POS with GST compliance, insurance claims, and split-payment support.</p>
</div>
<!-- Supplier -->
<div class="p-lg border border-outline-variant hover:border-primary transition-all group">
<div class="w-10 h-10 rounded bg-tertiary-fixed flex items-center justify-center mb-md group-hover:bg-tertiary group-hover:text-white transition-colors">
<span class="material-symbols-outlined" data-icon="local_shipping">local_shipping</span>
</div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-xs">Supplier Sync</h3>
<p class="text-on-surface-variant text-body-sm leading-relaxed">Centralized procurement with automated PO generation and supplier performance analytics.</p>
</div>
<!-- AI Insights -->
<div class="p-lg border border-outline-variant hover:border-primary transition-all group">
<div class="w-10 h-10 rounded bg-surface-container-highest flex items-center justify-center mb-md group-hover:bg-on-surface group-hover:text-white transition-colors">
<span class="material-symbols-outlined" data-icon="neurology">neurology</span>
</div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-xs">AI Insights</h3>
<p class="text-on-surface-variant text-body-sm leading-relaxed">ML-powered demand forecasting and smart reordering to prevent stock-outs.</p>
</div>
</div>
</div>
</section>
<!-- "Built for Growth" Section -->
<section id="solutions" class="py-xl bg-surface-container-low border-y border-outline-variant scroll-mt-16">
<div class="max-w-container-max mx-auto px-lg">
<div class="grid grid-cols-1 lg:grid-cols-2 gap-xl items-center">
<div class="aspect-video w-full rounded-lg shadow-lg border border-outline-variant bg-cover bg-center" data-alt="A clean, professional 3D isometric representation of interconnected pharmacy branches linked to a central cloud server." style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDdRIg0FS0e9szU7tVdmWpg3iBMlhCwLU-Rz8z5RWZ3U9yKDGVgnmLvXWIWfICHABv0Xk4jyYVZ13u3qOK0HZwwUbaoXiXQdWuQ3H3vYA2A5mRXo6sjU2-dAmMeSoi77iL7Xxi6TXc1ZJEsW9Q22PtIzGKkKzs4TcQUKeXiOzA7CTqmwvrW5dJPMq3_xyFE-vv_vGWdU4T37mhL7JPiQ_7wqD6Ogt8t1YcSKPVbxHz9laHlBUuZfWALKcNCi-69irNZyoZYnUjXMpM')"></div>
<div class="flex flex-col gap-md">
<h2 class="font-display text-display text-on-surface">Architected for Multi-Branch Growth.</h2>
<div class="space-y-md">
<div class="flex gap-md">
<div class="mt-1 flex-shrink-0 w-5 h-5 rounded-full bg-secondary text-white flex items-center justify-center">
<span class="material-symbols-outlined !text-[14px]">check</span>
</div>
<div>
<h4 class="font-label-md text-on-surface">Centralized Control Tower</h4>
<p class="text-body-sm text-on-surface-variant">Monitor sales, inventory levels, and staff performance across 100+ branches from a single dashboard.</p>
</div>
</div>
<div class="flex gap-md">
<div class="mt-1 flex-shrink-0 w-5 h-5 rounded-full bg-secondary text-white flex items-center justify-center">
<span class="material-symbols-outlined !text-[14px]">check</span>
</div>
<div>
<h4 class="font-label-md text-on-surface">Bank-Grade Isolation</h4>
<p class="text-body-sm text-on-surface-variant">Secure multi-tenant cloud architecture ensures your data is isolated, encrypted, and backed up hourly.</p>
</div>
</div>
<div class="flex gap-md">
<div class="mt-1 flex-shrink-0 w-5 h-5 rounded-full bg-secondary text-white flex items-center justify-center">
<span class="material-symbols-outlined !text-[14px]">check</span>
</div>
<div>
<h4 class="font-label-md text-on-surface">Offline Resilience</h4>
<p class="text-body-sm text-on-surface-variant">Continue billing even during internet outages. Local-first sync handles reconnection automatically.</p>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Pricing Section -->
<section id="pricing" class="py-xl bg-background scroll-mt-16">
<div class="max-w-container-max mx-auto px-lg">
<div class="text-center mb-xl">
<h2 class="font-display text-display text-on-surface mb-xs">Scalable Pricing for Every Scale</h2>
<p class="text-on-surface-variant text-body-md">Predictable monthly costs with no hidden implementation fees.</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-lg max-w-5xl mx-auto">
<?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="bg-white border <?php echo e($loop->iteration == 2 ? 'border-2 border-primary' : 'border-outline-variant'); ?> p-lg flex flex-col gap-md relative">
<?php if($loop->iteration == 2): ?>
<div class="absolute top-0 right-lg -translate-y-1/2 bg-primary text-on-primary px-sm py-1 rounded-full text-[10px] font-bold uppercase tracking-widest">Most Popular</div>
<?php endif; ?>
<div>
<h3 class="font-headline-md text-headline-md text-on-surface"><?php echo e($plan->name); ?></h3>
<div class="mt-xs flex items-baseline gap-1">
<?php if(empty($plan->price_monthly) || $plan->price_monthly == 0): ?>
<span class="text-headline-lg font-display text-primary">Custom</span>
<?php else: ?>
<span class="text-display font-display text-primary">₹<?php echo e(rtrim(rtrim((string) $plan->price_monthly, '0'), '.')); ?></span>
<span class="text-body-sm text-on-surface-variant">/month</span>
<?php endif; ?>
</div>
</div>
<ul class="flex flex-col gap-sm flex-grow">
<li class="flex items-center gap-xs text-body-sm text-on-surface-variant">
<span class="material-symbols-outlined text-primary !text-[16px]">check_circle</span>
    <?php echo e($plan->max_branches == -1 || $plan->max_branches > 999 ? 'Unlimited Branches' : ($plan->max_branches == 1 ? 'Single Branch' : 'Up to ' . $plan->max_branches . ' Branches')); ?>

</li>
<li class="flex items-center gap-xs text-body-sm text-on-surface-variant">
<span class="material-symbols-outlined text-primary !text-[16px]">check_circle</span>
    <?php echo e($plan->max_users == -1 || $plan->max_users > 999 ? 'Unlimited Users' : 'Up to ' . $plan->max_users . ' Users'); ?>

</li>
<?php if($plan->api_access): ?>
<li class="flex items-center gap-xs text-body-sm text-on-surface-variant">
<span class="material-symbols-outlined text-primary !text-[16px]">check_circle</span>
    API Access
</li>
<?php endif; ?>
<?php if(is_array($plan->features)): ?>
<?php $__currentLoopData = $plan->features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<li class="flex items-center gap-xs text-body-sm text-on-surface-variant">
<span class="material-symbols-outlined text-primary !text-[16px]">check_circle</span>
    <?php echo e($feature); ?>

</li>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
</ul>
<a href="<?php echo e(empty($plan->price_monthly) || $plan->price_monthly == 0 ? '#contact' : $ctaUrl); ?>" class="w-full h-8 <?php echo e($loop->iteration == 2 ? 'bg-primary text-on-primary shadow-md hover:brightness-110' : 'border border-outline-variant hover:bg-surface-container text-on-surface'); ?> font-label-md transition-all flex items-center justify-center"><?php echo e(empty($plan->price_monthly) || $plan->price_monthly == 0 ? 'Contact Sales' : 'Select Plan'); ?></a>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
</div>
</section>
<!-- Final CTA -->
<section class="py-24 relative overflow-hidden bg-primary">
<div class="max-w-container-max mx-auto px-lg relative z-10 text-center flex flex-col items-center gap-md">
<h2 class="font-display text-display text-on-primary max-w-2xl">Ready to digitize your pharmacy operations?</h2>
<p class="text-on-primary opacity-80 text-body-lg max-w-xl">Join thousands of leading pharmacies using <?php echo e(config('app.name', 'ArogyaKart')); ?> to increase margins and reduce medication errors.</p>
<div class="flex items-center gap-md mt-md">
<a href="<?php echo e($ctaUrl); ?>" class="bg-white text-primary h-10 px-lg flex items-center justify-center font-label-md rounded shadow-xl hover:scale-105 transition-transform"><?php echo e(auth()->check() ? 'Open Dashboard' : 'Get Started Now'); ?></a>
<a href="#contact" class="text-on-primary h-10 px-lg flex items-center justify-center font-label-md border border-on-primary/30 rounded hover:bg-white/10 transition-colors">Talk to an Expert</a>
</div>
</div>
</section>
</main>
<!-- Footer -->
<footer id="contact" class="bg-surface-container-low border-t border-outline-variant scroll-mt-16">
<div class="grid grid-cols-1 md:grid-cols-4 gap-lg px-lg py-xl max-w-container-max mx-auto">
<div class="flex flex-col gap-md">
<span class="font-headline-md text-headline-md font-bold text-primary"><?php echo e(config('app.name', 'ArogyaKart')); ?></span>
<p class="text-body-sm text-on-surface-variant leading-relaxed">Precision-engineered software for the modern healthcare supply chain.</p>
</div>
<div class="flex flex-col gap-sm">
<h4 class="font-label-md text-on-surface uppercase tracking-wider text-[10px]">Product</h4>
<a class="text-body-sm text-on-surface-variant hover:text-primary" href="#features">Features</a>
<a class="text-body-sm text-on-surface-variant hover:text-primary" href="#solutions">Solutions</a>
<a class="text-body-sm text-on-surface-variant hover:text-primary" href="#pricing">Pricing</a>
</div>
<div class="flex flex-col gap-sm">
<h4 class="font-label-md text-on-surface uppercase tracking-wider text-[10px]">Company</h4>
<a class="text-body-sm text-on-surface-variant hover:text-primary" href="#">Careers</a>
<a class="text-body-sm text-on-surface-variant hover:text-primary" href="#">Contact Support</a>
<a class="text-body-sm text-on-surface-variant hover:text-primary" href="#">Privacy Policy</a>
</div>
<div class="flex flex-col gap-sm">
<h4 class="font-label-md text-on-surface uppercase tracking-wider text-[10px]">Get Started</h4>
<p class="text-body-sm text-on-surface-variant mb-xs">Create your pharmacy account in minutes.</p>
<a href="<?php echo e(route('register')); ?>" class="bg-primary text-on-primary h-8 px-md rounded font-label-md text-label-md shadow-sm hover:bg-primary-container transition-colors flex items-center justify-center gap-1 w-max">
<span class="material-symbols-outlined !text-[16px]">rocket_launch</span> Sign Up Free
</a>
</div>
</div>
<div class="max-w-container-max mx-auto px-lg py-md border-t border-outline-variant flex justify-between items-center">
<span class="text-body-sm text-on-surface-variant">© <?php echo e(date('Y')); ?> <?php echo e(config('app.name', 'ArogyaKart')); ?>. All rights reserved.</span>
<div class="flex gap-md">
<a href="<?php echo e(route('login')); ?>" class="text-body-sm text-on-surface-variant hover:text-primary transition-colors">Login</a>
<a href="<?php echo e(route('register')); ?>" class="text-body-sm text-on-surface-variant hover:text-primary transition-colors">Register</a>
</div>
</div>
</footer>
<script>
        // Micro-interactions for feature card hover lift
        document.querySelectorAll('.group').forEach(card => {
            card.addEventListener('mouseenter', () => { card.style.transform = 'translateY(-4px)'; });
            card.addEventListener('mouseleave', () => { card.style.transform = 'translateY(0)'; });
        });

        // Mobile navigation menu
        (function () {
            const toggle = document.getElementById('nav-toggle');
            const menu = document.getElementById('mobile-menu');
            const icon = document.getElementById('nav-toggle-icon');
            if (!toggle || !menu) return;

            const setOpen = (open) => {
                menu.classList.toggle('hidden', !open);
                toggle.setAttribute('aria-expanded', String(open));
                icon.textContent = open ? 'close' : 'menu';
            };

            toggle.addEventListener('click', () => setOpen(menu.classList.contains('hidden')));
            // Close after tapping any link inside the panel.
            menu.querySelectorAll('a').forEach(link => link.addEventListener('click', () => setOpen(false)));
        })();
    </script>
</body></html>
<?php /**PATH C:\Users\sachi\Desktop\arogya-kart\arogya-kart\resources\views/welcome.blade.php ENDPATH**/ ?>