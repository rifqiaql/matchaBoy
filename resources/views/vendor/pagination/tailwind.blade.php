@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        
        <!-- Tampilan Mobile (Layar Kecil) -->
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-300 cursor-not-allowed rounded-md">
                    {!! __('Sebelumnya') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:text-[#86A789] hover:bg-gray-50">
                    {!! __('Sebelumnya') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:text-[#86A789] hover:bg-gray-50">
                    {!! __('Selanjutnya') !!}
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-300 cursor-not-allowed rounded-md">
                    {!! __('Selanjutnya') !!}
                </span>
            @endif
        </div>

        <!-- Tampilan Desktop -->
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-600">
                    Menampilkan 
                    <span class="font-semibold text-gray-900">{{ $paginator->firstItem() }}</span> 
                    sampai 
                    <span class="font-semibold text-gray-900">{{ $paginator->lastItem() }}</span> 
                    dari 
                    <span class="font-semibold text-gray-900">{{ $paginator->total() }}</span> 
                    data transaksi
                </p>
            </div>

            <div>
                <ul class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    
                    {{-- Tombol Previous --}}
                    @if ($paginator->onFirstPage())
                        <li aria-disabled="true" aria-label="{{ __('Previous') }}">
                            <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-300 bg-gray-50 border border-gray-200 cursor-not-allowed rounded-l-md">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 hover:text-[#86A789] transition-colors" aria-label="{{ __('Previous') }}">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </li>
                    @endif

                    {{-- Elemen Angka Halaman --}}
                    @foreach ($elements as $element)
                        {{-- Tanda Tiga Titik (Separator) --}}
                        @if (is_string($element))
                            <li aria-disabled="true">
                                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default">
                                    {{ $element }}
                                </span>
                            </li>
                        @endif

                        {{-- Deretan Angka --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li aria-current="page">
                                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-bold text-white bg-[#86A789] border border-[#86A789] z-10">
                                            {{ $page }}
                                        </span>
                                    </li>
                                @else
                                    <li>
                                        <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 hover:text-[#86A789] transition-colors">
                                            {{ $page }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Tombol Next --}}
                    @if ($paginator->hasMorePages())
                        <li>
                            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 hover:text-[#86A789] transition-colors" aria-label="{{ __('Next') }}">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </li>
                    @else
                        <li aria-disabled="true" aria-label="{{ __('Next') }}">
                            <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-300 bg-gray-50 border border-gray-200 cursor-not-allowed rounded-r-md">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
@endif