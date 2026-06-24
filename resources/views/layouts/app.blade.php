<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'MatchaBoy' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-grey-100 font-sans antialiased text-gray-800">
    <div class="flex flex-col h-screen overflow-hidden">

        <x-header />

        <div class="flex flex-1 overflow-hidden">

            <x-sidebar />

            <main class="flex-1 overflow-y-auto bg-gray-50">
                @yield('content')
            </main>

        </div>
    </div>
</body>

</html>
