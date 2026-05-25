<div class="w-64 bg-[#86A789] text-white shadow-lg h-screen flex flex-col">
    <div class="p-6">
        <div class="flex items-center justify-center gap-2 mb-8">
            <div class="w-8 h-8 bg-white rounded-full"></div>
            <span class="text-lg font-bold">MATCHABOY</span>
        </div>
    </div>

    <nav class="flex-1 space-y-2 px-4">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl transition duration-300 {{ request()->routeIs('dashboard') ? 'bg-white/30 font-semibold' : 'hover:bg-white/10' }}">
            <x-icon name="home" size="md" class="w-6 h-6 stroke-current flex-shrink-0" />
            <span class="text-sm font-medium">Home</span>
        </a>
        <a href="{{ route('keranjang.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl transition duration-300 {{ request()->routeIs('keranjang.*') ? 'bg-white/30 font-semibold' : 'hover:bg-white/10' }}">
            <x-icon name="shopping-cart" size="md" class="w-6 h-6 stroke-current flex-shrink-0" />
            <span class="text-sm font-medium">Keranjang</span>
        </a>
        <a href="{{ route('inventory.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl transition duration-300 {{ request()->routeIs('inventory.*') ? 'bg-white/30 font-semibold' : 'hover:bg-white/10' }}">
            <x-icon name="archive-box" size="md" class="w-6 h-6 stroke-current flex-shrink-0" />
            <span class="text-sm font-medium">Gudang</span>
        </a>
        <a href="{{ route('laporan.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-xl transition duration-300 {{ request()->routeIs('laporan.*') ? 'bg-white/30 font-semibold' : 'hover:bg-white/10' }}">
            <x-icon name="chart-bar" size="md" class="w-6 h-6 stroke-current flex-shrink-0" />
            <span class="text-sm font-medium">Laporan</span>
        </a>
    </nav>
</div>
