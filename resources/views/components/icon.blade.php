{{-- Reusable Icon Component using Heroicons --}}
@props([
    'name' => 'question-mark-circle',
    'size' => 'md',
    'class' => '',
])

@php
    $sizeMap = [
        'xs' => 16,
        'sm' => 20,
        'md' => 24,
        'lg' => 32,
        'xl' => 40,
    ];

    $iconSize = $sizeMap[$size] ?? 24;
    $iconPath = resource_path("../node_modules/heroicons/24/outline/{$name}.svg");

    // Fallback to solid if outline doesn't exist
    if (!file_exists($iconPath)) {
        $iconPath = resource_path("../node_modules/heroicons/24/solid/{$name}.svg");
    }
@endphp

@if (file_exists($iconPath))
    @php
        $svg = file_get_contents($iconPath);
        // Ensure SVG uses currentColor for proper theme inheritance
        $svg = str_replace(['stroke="none"', 'stroke="#000"', 'stroke="black"'], 'stroke="currentColor"', $svg);
        $svg = str_replace(['fill="none"', 'fill="#000"', 'fill="black"'], 'fill="none"', $svg);
    @endphp
    <span class="icon {{ $class }}" style="display: inline-block; width: 1em; height: 1em;">
        {!! $svg !!}
    </span>
@else
    <span class="inline-block text-gray-400" title="Icon '{{ $name }}' not found">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </span>
@endif

