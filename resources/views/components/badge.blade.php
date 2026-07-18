@props(['color' => 'gray'])

@php
    $map = [
        'gray' => 'bg-gray-100 text-gray-700',
        'green' => 'bg-emerald-100 text-emerald-700',
        'red' => 'bg-red-100 text-red-700',
        'blue' => 'bg-blue-100 text-blue-700',
    ];
    $classes = $map[$color] ?? $map['gray'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $classes]) }}>
    {{ $slot }}
</span>
