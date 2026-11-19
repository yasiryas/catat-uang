@props(['name' => 'dashboard', 'class' => ''])

<svg aria-hidden="true" class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor"
    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" shape-rendering="geometricPrecision"
    xmlns="http://www.w3.org/2000/svg">
    @switch($name)
        @case('dashboard')
            <rect width="7" height="9" x="3" y="3" rx="2" />
            <rect width="7" height="5" x="14" y="3" rx="2" />
            <rect width="7" height="9" x="14" y="12" rx="2" />
            <rect width="7" height="5" x="3" y="16" rx="2" />
            @break
        @case('category')
            <path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z" />
            @break
        @case('wallet')
            <path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1" />
            <path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4" />
            @break
        @case('income')
            <path d="M12 17V3" />
            <path d="m6 11 6 6 6-6" />
            <path d="M19 21H5" />
            @break
        @case('expense')
            <path d="M12 21V7" />
            <path d="m6 13 6-6 6 6" />
            <path d="M19 3H5" />
            @break
        @case('goal')
            <circle cx="12" cy="12" r="10" />
            <circle cx="12" cy="12" r="6" />
            <circle cx="12" cy="12" r="2" />
            @break
        @case('mutation')
            <path d="M8 3 4 7l4 4" />
            <path d="M4 7h16" />
            <path d="m16 21 4-4-4-4" />
            <path d="M20 17H4" />
            @break
        @case('period')
            <path d="M8 2v4" />
            <path d="M16 2v4" />
            <rect width="18" height="18" x="3" y="4" rx="3" />
            <path d="M3 10h18" />
            @break
        @case('adjustment')
            <path d="M21 4h-7M10 4H3M21 12h-9M8 12H3M21 20h-5M12 20H3" />
            <path d="M14 2v4M8 10v4M16 18v4" />
            @break
        @case('family')
            <path d="M18 21a8 8 0 0 0-16 0" />
            <circle cx="10" cy="8" r="5" />
            <path d="M22 20c0-3.37-1.9-6.11-4.5-7.5" />
            <path d="M20 12a4 4 0 0 0 0-8" />
            @break
        @case('user')
            <circle cx="12" cy="8" r="5" />
            <path d="M20 21a8 8 0 0 0-16 0" />
            @break
        @case('logout')
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <path d="m16 17 5-5-5-5" />
            <path d="M21 12H9" />
            @break
        @case('check')
            <circle cx="12" cy="12" r="10" />
            <path d="m9 12 2 2 4-4" />
            @break
        @case('warning')
            <circle cx="12" cy="12" r="10" />
            <path d="M12 8v4" />
            <path d="M12 16h.01" />
            @break
        @case('menu')
            <path d="M4 6h16M4 12h16M4 18h16" />
            @break
        @case('collapse')
            <path d="m11 17-5-5 5-5" />
            <path d="m18 17-5-5 5-5" />
            @break
        @case('chevron')
            <path d="m6 9 6 6 6-6" />
            @break
        @case('close')
            <path d="M18 6 6 18" />
            <path d="m6 6 12 12" />
            @break
        @case('plus')
            <path d="M5 12h14" />
            <path d="M12 5v14" />
            @break
        @case('trash')
            <path d="M3 6h18" />
            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
            <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
            <path d="M10 11v6M14 11v6" />
            @break
        @case('chart')
            <path d="M3 3v16a2 2 0 0 0 2 2h16" />
            <path d="M18 16V8" />
            <path d="M12 16v-5" />
            <path d="M6 16V4" />
            @break
        @case('receipt')
            <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z" />
            <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8" />
            <path d="M12 17V7" />
            @break
        @case('bank')
            <path d="M3 21h18" />
            <path d="M5 21V10M19 21V10M9 21V10M15 21V10" />
            <path d="m12 2 9 7H3z" />
            @break
        @case('cash')
            <rect width="20" height="12" x="2" y="6" rx="3" />
            <circle cx="12" cy="12" r="2" />
            <path d="M6 12h.01M18 12h.01" />
            @break
        @case('ewallet')
            <rect width="14" height="20" x="5" y="2" rx="3" />
            <path d="M12 18h.01" />
            @break
        @default
            <rect width="7" height="9" x="3" y="3" rx="2" />
            <rect width="7" height="5" x="14" y="3" rx="2" />
            <rect width="7" height="9" x="14" y="12" rx="2" />
            <rect width="7" height="5" x="3" y="16" rx="2" />
    @endswitch
</svg>
