@props([
    'title',
    'subtitle' => null,
])

<header {{ $attributes->class('space-y-1 border-b border-line pb-5') }}>
    <h2 class="text-xl font-semibold tracking-tight text-ink">{{ $title }}</h2>
    @if (filled((string) $subtitle))
        <p class="text-sm text-ink-muted">{{ $subtitle }}</p>
    @endif
</header>
