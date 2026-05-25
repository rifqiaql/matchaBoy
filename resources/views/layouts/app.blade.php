<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'MatchaBoy' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-grey-100 font-sans antialiased text-gray-800">
    <div class="flex h-screen overflow-hidden">

        <x-sidebar />

        <div class="flex-1 flex flex-col overflow-hidden">

            <x-header />

            <main class="flex-1 overflow-y-auto bg-gray-50">
                @yield('content')
            </main>

        </div>
    </div>
</body>
</html>
