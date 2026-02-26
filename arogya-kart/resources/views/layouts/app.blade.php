<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">

    <div class="flex">
        @yield('sidebar')

        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

</body>
</html>