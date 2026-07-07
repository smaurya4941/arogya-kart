<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 — Server Error | {{ config('app.name', 'ArogyaKart') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 antialiased min-h-screen flex items-center justify-center">
    <div class="text-center px-6 py-20 max-w-lg mx-auto">
        {{-- Icon --}}
        <div class="flex justify-center mb-6">
            <div class="w-24 h-24 rounded-full bg-rose-50 flex items-center justify-center ring-8 ring-rose-100">
                <svg class="w-12 h-12 text-rose-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                </svg>
            </div>
        </div>

        {{-- Code --}}
        <p class="text-8xl font-black text-rose-500 tracking-tight leading-none mb-4">500</p>

        {{-- Heading --}}
        <h1 class="text-2xl font-bold text-slate-800 mb-3">Something went wrong</h1>
        <p class="text-slate-500 mb-8 leading-relaxed">
            We're sorry — an unexpected error occurred on our end. Our team has been notified.
            Please try again in a moment or contact support if the problem persists.
        </p>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="javascript:history.back()"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-medium hover:bg-slate-200 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Go Back
            </a>
            @auth
                <a href="{{ route('admin.dashboard') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-rose-600 text-white font-medium hover:bg-rose-700 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                    </svg>
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-rose-600 text-white font-medium hover:bg-rose-700 transition shadow-sm">
                    Sign In
                </a>
            @endauth
        </div>

        <p class="mt-12 text-sm text-slate-400">
            {{ config('app.name', 'ArogyaKart') }} &mdash; Pharmacy Management
        </p>
    </div>
</body>
</html>
