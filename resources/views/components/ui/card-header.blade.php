@props([
    'title',
    'subtitle' => null,
])

<header {{ $attributes->class('space-y-1') }}>
    <h2 class="text-2xl font-semibold tracking-wide">{{ $title }}</h2>
    @if (filled((string) $subtitle))
        <p class="text-xs text-green-200">{{ $subtitle }}</p>
    @endif
</header>
