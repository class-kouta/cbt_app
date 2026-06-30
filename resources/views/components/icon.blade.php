@props([
    'name',
])

@php
    $pathMarkup = \App\Support\Heroicons::paths()[$name] ?? '';
    $defaultClasses = ['inline-block', 'shrink-0'];
    if ($name === 'trash') {
        $defaultClasses[] = 'text-gray-500';
    }
@endphp

@if ($pathMarkup === '')
    <!-- Unknown icon: {{ $name }} -->
@else
    <svg
        {{ $attributes->class($defaultClasses)->merge([
            'xmlns' => 'http://www.w3.org/2000/svg',
            'viewBox' => '0 0 24 24',
            'fill' => 'none',
            'aria-hidden' => 'true',
        ]) }}
    >
        {!! $pathMarkup !!}
    </svg>
@endif
