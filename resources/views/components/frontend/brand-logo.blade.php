@props([
    'showWordmark' => true,
    'size' => 'md',
])

@php
    $iconClass = match ($size) {
        'sm' => 'size-8',
        'lg' => 'size-12',
        default => 'size-9',
    };

    $textClass = match ($size) {
        'sm' => 'text-base',
        'lg' => 'text-2xl',
        default => 'text-lg',
    };
@endphp

<span {{ $attributes->class('inline-flex items-center gap-2.5 select-none') }}>
    <img
        src="{{ asset('images/logo-mark.svg') }}"
        alt="{{ config('app.name') }}"
        width="36"
        height="36"
        class="{{ $iconClass }} shrink-0 drop-shadow-xs transition-transform duration-200 group-hover:scale-105"
    >
    @if($showWordmark)
        <span class="font-display {{ $textClass }} font-extrabold tracking-tight text-slate-950 leading-none">
            Panora<span class="text-primary">Goods</span>
        </span>
    @endif
</span>
