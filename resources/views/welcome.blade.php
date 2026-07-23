<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'MatchaBoy') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
        }

        .bg-matcha {
            background-color: #2D5A34;
        }

        .text-matcha {
            color: #2D5A34;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 flex items-center justify-center min-h-screen flex-col relative overflow-hidden">

    <!-- Ornamen Latar Belakang (Soft) -->
    <div
        class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-[#86A789] rounded-full mix-blend-multiply filter blur-3xl opacity-20">
    </div>
    <div
        class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-[#4A7C59] rounded-full mix-blend-multiply filter blur-3xl opacity-20">
    </div>

    <main class="relative z-10 w-full max-w-md p-8 flex flex-col items-center text-center">

        <!-- Logo Brand -->
        <div class="mb-8 p-4 bg-white rounded-full shadow-sm border border-gray-100 flex items-center justify-center">
            <img src="{{ asset('images/logo.png') }}" alt="MatchaBoy Logo" class="h-24 w-auto object-contain"
                onerror="this.onerror=null; this.innerHTML='<span class=\'text-2xl font-bold text-matcha\'>MatchaBoy</span>'">
        </div>

        <h1 class="text-4xl font-bold text-gray-900 tracking-tight mb-3">
            Welcome to <span class="text-matcha">MatchaBoy</span>
        </h1>

        <p class="text-gray-500 mb-10 text-base leading-relaxed">
            Sistem Kasir Terintegrasi & Prediksi Kebutuhan Inventaris Menggunakan Single Moving Average.
        </p>

        <!-- Navigation Box -->
        <div class="bg-white w-full p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col gap-4">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/keranjang') }}"
                        class="w-full flex items-center justify-center gap-2 py-3 bg-matcha hover:bg-opacity-90 text-white rounded-xl font-semibold transition-all shadow-sm">
                        Masuk ke Dashboard
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="w-full flex items-center justify-center gap-2 py-3 bg-matcha hover:bg-opacity-90 text-white rounded-xl font-semibold transition-all shadow-sm">
                        Log In
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="w-full flex items-center justify-center py-3 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-xl font-medium transition-all">
                            Buat Akun Baru
                        </a>
                    @endif
                @endauth
            @endif
        </div>

    </main>

    <!-- Footer Footer -->
    <footer class="absolute bottom-6 text-sm text-gray-400 font-medium">
        &copy; {{ date('Y') }} MatchaBoy POS & Inventory System
    </footer>

</body>

</html>
