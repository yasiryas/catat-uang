@props(['action', 'confirm' => 'Anda yakin ingin menghapus?'])

<form action="{{ $action }}" method="POST" class="inline">
    @csrf
    @method('DELETE')
    <button type="submit" {{ $attributes->merge(['class' => 'text-red-600 hover:underline inline-flex items-center']) }}
        onclick="return confirm({{ $confirm ? json_encode($confirm) : json_encode('Anda yakin ingin menghapus?') }})">
        {{ $slot }}
    </button>
</form>
