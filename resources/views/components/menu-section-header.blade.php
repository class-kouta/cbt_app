@props([
    'title',
    'isFirst' => false,
])

<div @class([
    'menu-section-header px-6 pt-3 pb-1',
    'border-t-2 border-gray-400/50 mt-1' => !$isFirst,
])>
    <p class="text-xs font-bold text-gray-500 tracking-wide">{{ $title }}</p>
</div>
