@props([
    'title',
    'description',
    'isLast' => false,
])

<section @class([
    'max-w-4xl mx-auto mb-6 sm:mb-8 pb-6 sm:pb-8',
    'border-b border-gray-300/70' => !$isLast,
])>
    <div class="mb-4 sm:mb-6">
        <h2 class="text-base sm:text-lg md:text-xl font-bold text-gray-800">{{ $title }}</h2>
        <p class="mt-1 text-xs sm:text-sm text-gray-600">{{ $description }}</p>
    </div>
    <div class="grid grid-cols-2 gap-3 sm:gap-4 md:gap-6">
        {{ $slot }}
    </div>
</section>
