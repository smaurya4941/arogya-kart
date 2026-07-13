<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Under Maintenance | {{ config('app.name', 'ArogyaKart') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-background text-on-surface antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-6 text-center">
        <span class="material-symbols-outlined text-6xl text-primary">construction</span>
        <h1 class="mt-4 text-2xl font-bold">We&rsquo;ll be right back</h1>
        <p class="mt-2 max-w-md text-on-surface-variant">{{ $message }}</p>
        <p class="mt-6 text-xs text-on-surface-variant">{{ config('app.name', 'ArogyaKart') }} &middot; {{ now()->format('d M Y, h:i A') }}</p>
    </div>
</body>
</html>
