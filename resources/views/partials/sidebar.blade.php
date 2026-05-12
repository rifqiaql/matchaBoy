<div class="w-64 bg-green-700 text-white shadow-lg">
    <div class="p-6">
        <div class="flex items-center gap-2 mb-8">
            <div class="w-8 h-8 bg-white rounded-full"></div>
            <span class="text-xl font-bold">MATCHABOY</span>
        </div>
    </div>

    <nav class="space-y-1">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-6 py-3 rounded-r-lg transition transform {{ request()->routeIs('dashboard') ? 'bg-green-500 font-semibold shadow-lg scale-105' : 'hover:bg-green-600' }}">
            <x-icon name="home" size="md" class="w-5 h-5 stroke-current flex-shrink-0" />
            <span>Home</span>
        </a>
        <a href="{{ route('keranjang.index') }}" class="flex items-center gap-3 px-6 py-3 rounded-r-lg transition transform {{ request()->routeIs('keranjang.*') ? 'bg-green-500 font-semibold shadow-lg scale-105' : 'hover:bg-green-600' }}">
            <x-icon name="shopping-cart" size="md" class="w-5 h-5 stroke-current flex-shrink-0" />
            <span>Keranjang</span>
        </a>
        <a href="{{ route('inventory.index') }}" class="flex items-center gap-3 px-6 py-3 rounded-r-lg transition transform {{ request()->routeIs('inventory.*') ? 'bg-green-500 font-semibold shadow-lg scale-105' : 'hover:bg-green-600' }}">
            <x-icon name="archive-box" size="md" class="w-5 h-5 stroke-current flex-shrink-0" />
            <span>Gudang</span>
        </a>
        <a href="{{ route('laporan.index') }}" class="flex items-center gap-3 px-6 py-3 rounded-r-lg transition transform {{ request()->routeIs('laporan.*') ? 'bg-green-500 font-semibold shadow-lg scale-105' : 'hover:bg-green-600' }}">
            <x-icon name="chart-bar" size="md" class="w-5 h-5 stroke-current flex-shrink-0" />
            <span>Laporan</span>
        </a>
    </nav>
</div>
