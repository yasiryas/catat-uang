@props(['modalName', 'action', 'message' => 'Anda yakin ingin menghapus?', 'method' => 'DELETE'])

<button type="button" {{ $attributes->merge(['class' => 'inline-flex items-center justify-center']) }}
    x-on:click.prevent="$dispatch('open-modal', '{{ $modalName }}')" data-action="{{ $action }}"
    data-message="{{ $message }}">
    {{ $slot }}
</button>
