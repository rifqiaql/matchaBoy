<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'MatchaBoy' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-100 font-sans antialiased text-gray-800 h-screen flex flex-col overflow-hidden">

    <header
        class="bg-dark-matcha text-white px-8 py-4 flex items-center justify-between shadow rounded-b-xl shrink-0 z-40 relative">
        <div class="flex items-center gap-10">
            <img src="{{ asset('images/logo.png') }}" alt="Logo MatchaBoy" class="h-20 w-auto">
            <div class="flex items-center gap-4">
                <p id="app-clock-time" class="text-lg font-bold tracking-wide"></p>
                <p id="app-clock-date" class="text-sm opacity-90 pt-0.5"></p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                <x-icon name="user-circle" size="md" class="w-6 h-6 stroke-current text-dark-matcha" />
            </div>
            <div>
                <p class="font-semibold text-sm">{{ Auth::user()->name ?? 'Guest' }}</p>
                <!-- REVISI: ucfirst agar huruf depan role otomatis kapital (Admin / Karyawan) -->
                <p class="text-xs opacity-90">{{ ucfirst(Auth::user()->role ?? 'Admin') }}</p>
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

    <div class="flex flex-1 overflow-hidden min-h-0">
        <!-- INI ADALAH KOMPONEN SIDEBAR ASLINYA -->
        <x-sidebar />

        <main class="flex-1 min-h-0 overflow-y-auto p-8">
            @yield('content')
        </main>
    </div>

    <!-- NOTIFICATION SYSTEM -->
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

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const timeEl = document.getElementById('app-clock-time');
            const dateEl = document.getElementById('app-clock-date');

            if (!timeEl || !dateEl) return;

            const timeFormatter = new Intl.DateTimeFormat('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true,
            });

            const dateFormatter = new Intl.DateTimeFormat('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric',
            });

            const updateClock = () => {
                const now = new Date();
                timeEl.textContent = timeFormatter.format(now).replace(/\s/g, ' ');
                dateEl.textContent = dateFormatter.format(now);
            };

            updateClock();
            setInterval(updateClock, 1000);
        });
    </script>
</body>

</html>
