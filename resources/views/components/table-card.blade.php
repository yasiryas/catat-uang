<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden']) }}>
    <table class="w-full">
        {{ $slot }}
    </table>
</div>

