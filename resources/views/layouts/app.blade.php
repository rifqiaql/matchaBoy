<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'MatchaBoy' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-100 font-sans antialiased text-gray-800">

    <header
        class="bg-dark-matcha text-white px-8 py-4 flex items-center justify-between shadow rounded-b-xl w-full z-40 relative">
        <div class="flex items-center gap-10">
            <img src="{{ asset('images/logo.png') }}" alt="Logo MatchaBoy" class="h-20 w-auto">
            <div class="flex items-center gap-4">
                <p class="text-lg font-bold tracking-wide">12.02.03 PM</p>
                <p class="text-sm opacity-90 pt-0.5">Minggu, 5 September 2026</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                <x-icon name="user-circle" size="md" class="w-6 h-6 stroke-current text-dark-matcha" />
            </div>
            <div>
                <p class="font-semibold text-sm">{{ Auth::user()->name ?? 'Guest' }}</p>
                <p class="text-xs opacity-90">{{ Auth::user()->role ?? 'Admin' }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="ml-2">
                @csrf
                <button type="submit" class="text-lg hover:scale-110 transition-transform cursor-pointer"
                    title="Logout">
                    <x-icons.logout class="w-6 h-6 text-white" />
                </button>
            </form>
        </div>
    </header>

    <div class="flex h-[calc(100vh-100px)] overflow-hidden">
        <x-sidebar />

        <main class="flex-1 overflow-y-auto p-8">
            @yield('content')
        </main>
    </div>

    <div x-data="{ show: false, message: '', type: 'success' }"
        x-on:notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 3000)"
        class="fixed top-20 left-0 right-0 z-50 flex justify-center pointer-events-none">
        <div x-show="show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-end="opacity-0 -translate-y-2"
            class="flex items-center gap-3 px-6 py-4 rounded-xl shadow-lg border text-white pointer-events-auto"
            :class="type === 'success' ? 'bg-[#365E3F] border-white' : 'bg-red-600 border-red-700'">
            <span x-text="type === 'success' ? '✓' : '⚠'"></span>
            <p x-text="message" class="text-sm font-medium"></p>
        </div>
    </div>
</body>

</html>
