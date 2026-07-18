<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow overflow-hidden']) }}>
    <table class="w-full">
        {{ $slot }}
    </table>
</div>

