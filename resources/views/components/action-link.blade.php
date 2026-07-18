@props(['href'])

<a href="{{ $href }}"
    {{ $attributes->merge(['class' => 'text-blue-600 hover:underline inline-flex items-center']) }}>
    {{ $slot }}
</a>
