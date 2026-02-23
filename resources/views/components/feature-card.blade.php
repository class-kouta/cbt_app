@props([
    'href',
    'title',
])

<a href="{{ $href }}" class="block group">
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 md:p-8 border border-gray-200 hover:shadow-xl transition-all duration-300 hover:scale-105 h-full">
        <div class="flex flex-col items-center text-center">
            <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-16 md:h-16 mb-3 sm:mb-4 md:mb-6 text-green-600">
                {{ $icon }}
            </div>
            <h2 class="text-sm sm:text-base md:text-xl lg:text-2xl font-bold text-gray-900">{{ $title }}</h2>
        </div>
    </div>
</a>
