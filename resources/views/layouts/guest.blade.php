<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ArogyaKart') }}</title>

        <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
        <style>.material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400;vertical-align:middle;}</style>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-on-surface antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center bg-background px-4 py-10">
            <a href="/" class="mb-6 flex items-center gap-2.5">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-on-primary shadow-sm">
                    <span class="material-symbols-outlined text-[24px]" style="font-variation-settings:'FILL' 1;">medical_services</span>
                </div>
                <div class="leading-tight">
                    <h1 class="text-lg font-bold tracking-tight text-on-surface">{{ config('app.name', 'ArogyaKart') }}</h1>
                    <p class="text-[10px] font-medium uppercase tracking-widest text-on-surface-variant/70">Pharmacy OS</p>
                </div>
            </a>

            <div class="card w-full max-w-md p-6 sm:p-8">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
