@props([
    'xModel' => null,
    'placeholder' => 'Pilih...',
    'required' => false,
    'name' => null,
    'id' => null,
    'variant' => 'input',
    'disabled' => false,
    'items' => [],
    'alpineItems' => null,
    'valueKey' => 'id',
    'labelKey' => 'name',
    'value' => null,
])

@php
$isFilter = $variant === 'filter';
$uid = $id ?? 'cs-' . md5($xModel ?? $name ?? $placeholder . rand());
@endphp

<div
    x-data="{
        open: false,
        search: '',
        selectedLabel: '',
        selectedValue: '',
        select(value) {
            const sel = $refs.hiddenSelect;
            if (!sel) return;
            sel.value = value;
            sel.dispatchEvent(new Event('input', { bubbles: true }));
            sel.dispatchEvent(new Event('change', { bubbles: true }));
            this.syncDisplay();
            this.open = false;
            this.search = '';
        },
        syncDisplay() {
            const sel = $refs.hiddenSelect;
            if (!sel) return;
            this.selectedValue = sel.value;
            const idx = sel.selectedIndex;
            this.selectedLabel = idx >= 0 && sel.options[idx] ? sel.options[idx].text : '';
        },
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.search = '';
                this.$nextTick(() => $refs.searchInput?.focus());
            }
        },
        close() {
            this.open = false;
            this.search = '';
        },
        @if($alpineItems)
        get filteredItems() {
            const items = {{ $alpineItems }} || [];
            if (!this.search) return items;
            const q = this.search.toLowerCase();
            return items.filter(i => (i['{{ $labelKey }}'] || '').toLowerCase().includes(q));
        },
        @endif
        init() {
            this.$nextTick(() => {
                @if(!$xModel && $value !== null)
                const sel = $refs.hiddenSelect;
                if (sel) sel.value = '{{ $value }}';
                @endif
                this.syncDisplay();
            });
        },
    }"
    @keydown.escape="close()"
    x-id="['cs-{{ $uid }}']"
    class="relative"
>
    <select x-ref="hiddenSelect"
            @if($xModel) x-model="{{ $xModel }}" @endif
            @change="syncDisplay()"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($name) name="{{ $name }}" @endif
            class="sr-only" aria-hidden="true" tabindex="-1">
        @if($alpineItems)
            <option value=""></option>
            <template x-for="item in {{ $alpineItems }}" :key="item['{{ $valueKey }}']">
                <option :value="item['{{ $valueKey }}']" x-text="item['{{ $labelKey }}']"></option>
            </template>
        @else
            <option value="">{{ $placeholder }}</option>
            @foreach($items as $item)
                <option value="{{ $item[$valueKey] ?? '' }}">{{ $item[$labelKey] ?? '' }}</option>
            @endforeach
        @endif
    </select>

    <button type="button" @click="toggle()"
            :id="$id('cs-{{ $uid }}', 'button')"
            class="w-full text-left flex items-center justify-between gap-2 {{ $isFilter ? 'select-filter' : 'select-input' }}"
            :class="{ 'ring-2 ring-blue-500 border-blue-500': open }"
            aria-haspopup="listbox"
            :aria-expanded="open"
            :aria-labelledby="$id('cs-{{ $uid }}', 'button')">
        <span class="truncate" x-text="selectedLabel || '{{ $placeholder }}'" :class="{ 'text-slate-400': !selectedLabel }"></span>
        <svg class="w-4 h-4 text-slate-400 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6"/>
        </svg>
    </button>

    <div x-show="open" x-cloak
         @click.away="close()"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-1"
         class="absolute z-[9999] mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden"
         role="listbox"
         :aria-labelledby="$id('cs-{{ $uid }}', 'button')">

        @if($isFilter && $alpineItems)
        <div class="p-2 border-b border-slate-100">
            <input type="text" x-ref="searchInput" x-model="search"
                   class="w-full px-3 py-1.5 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                   placeholder="Cari..."
                   @keydown.escape="close()"
                   @keydown.down.prevent="$nextTick(() => $el.closest('[role=listbox]').querySelector('[role=option]:not([style*=\\'display: none\\'])')?.focus())">
        </div>
        @endif

        <div class="max-h-60 overflow-y-auto p-1.5 space-y-0.5">
            @if($alpineItems)
                <template x-if="filteredItems.length === 0">
                    <div class="px-3 py-2 text-sm text-slate-400 text-center">Tidak ada data</div>
                </template>
                <template x-for="(item, index) in filteredItems" :key="item['{{ $valueKey }}']">
                    <div @click="select(item['{{ $valueKey }}'])"
                         @keydown.enter="select(item['{{ $valueKey }}'])"
                         :class="'px-3 py-2 text-sm rounded-lg cursor-pointer transition-colors flex items-center justify-between ' + (item['{{ $valueKey }}'] == selectedValue ? 'bg-blue-50 text-blue-700 font-medium' : 'text-slate-700 hover:bg-slate-100')"
                         role="option"
                         :aria-selected="item['{{ $valueKey }}'] == selectedValue"
                         tabindex="0">
                        <span x-text="item['{{ $labelKey }}']"></span>
                        <svg x-show="item['{{ $valueKey }}'] == selectedValue" class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </template>
            @else
                @forelse($items as $item)
                    @php
                        $iv = $item[$valueKey] ?? '';
                        $il = $item[$labelKey] ?? '';
                    @endphp
                    <div @click="select('{{ $iv }}')"
                         :class="'px-3 py-2 text-sm rounded-lg cursor-pointer transition-colors flex items-center justify-between ' + ('{{ $iv }}' == selectedValue ? 'bg-blue-50 text-blue-700 font-medium' : 'text-slate-700 hover:bg-slate-100')"
                         role="option"
                         :aria-selected="'{{ $iv }}' == selectedValue"
                         tabindex="0">
                        <span>{{ $il }}</span>
                        <svg x-show="'{{ $iv }}' == selectedValue" class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                @empty
                    <div class="px-3 py-2 text-sm text-slate-400 text-center">Tidak ada data</div>
                @endforelse
            @endif
        </div>
    </div>
</div>
