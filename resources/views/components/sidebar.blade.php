<div class="w-32 bg-white h-screen shadow-md flex flex-col items-center pt-8 flex-shrink-0">

    <div class="mb-35">
    </div>

    <nav class="flex flex-col gap-6">

        <a href="{{ route('dashboard') }}"
            class="flex flex-col items-center justify-center w-24 h-24 rounded-2xl transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-[#86A789] text-white shadow-lg transform -translate-y-1' : 'text-black hover:bg-[#86A789] hover:text-white hover:-translate-y-1' }}">
            <x-icon name="home" class="w-7 h-7 mb-1 stroke-current" />
            <span class="text-sm font-semibold">Home</span>
        </a>

        <a href="{{ route('keranjang.index') }}"
            class="flex flex-col items-center justify-center w-24 h-24 rounded-2xl transition-all duration-300 {{ request()->routeIs('keranjang.*') ? 'bg-[#86A789] text-white shadow-lg transform -translate-y-1' : 'text-black hover:bg-[#86A789] hover:text-white hover:-translate-y-1' }}">
            <x-icon name="shopping-cart" class="w-7 h-7 mb-1 stroke-current" />
            <span class="text-sm font-semibold">Keranjang</span>
        </a>

        <a href="{{ route('inventory.index') }}"
            class="flex flex-col items-center justify-center w-24 h-24 rounded-2xl transition-all duration-300 {{ request()->routeIs('inventory.*') ? 'bg-[#86A789] text-white shadow-lg transform -translate-y-1' : 'text-black hover:bg-[#86A789] hover:text-white hover:-translate-y-1' }}">
            <x-icon name="archive-box" class="w-7 h-7 mb-1 stroke-current" />
            <span class="text-sm font-semibold">Gudang</span>
        </a>

        <a href="{{ route('laporan.index') }}"
            class="flex flex-col items-center justify-center w-24 h-24 rounded-2xl transition-all duration-300 {{ request()->routeIs('laporan.*') ? 'bg-[#86A789] text-white shadow-lg transform -translate-y-1' : 'text-black hover:bg-[#86A789] hover:text-white hover:-translate-y-1' }}">
            <x-icon name="chart-bar" class="w-7 h-7 mb-1 stroke-current" />
            <span class="text-sm font-semibold">Laporan</span>
        </a>

    </nav>
</div>
