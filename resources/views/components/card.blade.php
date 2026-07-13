@props([
    'title',
    'value',
    'description' => null,
])

<div {{ $attributes->class('card card-pad') }}>
    <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">{{ $title }}</p>
    <p class="mt-1 text-2xl font-bold tracking-tight text-on-surface">{{ $value }}</p>

    @if ($description)
        <p class="mt-1 text-xs text-on-surface-variant">{{ $description }}</p>
    @endif
</div>
