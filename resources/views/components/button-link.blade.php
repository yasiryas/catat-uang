@props(['href', 'method' => null, 'confirm' => null])

@if ($method)
    <form action="{{ $href }}" method="POST" class="inline">
        @csrf
        @method($method)
        @if ($confirm)
            <button type="submit"
                {{ $attributes->merge(['class' => 'inline-flex items-center px-4 py-2 rounded-lg transition']) }}
                onclick="return confirm(@js($confirm))">
                {{ $slot }}
            </button>
        @else
            <button type="submit"
                {{ $attributes->merge(['class' => 'inline-flex items-center px-4 py-2 rounded-lg transition']) }}>
                {{ $slot }}
            </button>
        @endif
    </form>
@else
    <a href="{{ $href }}"
        {{ $attributes->merge(['class' => 'inline-flex items-center px-4 py-2 rounded-lg transition']) }}>
        {{ $slot }}
    </a>
@endif
