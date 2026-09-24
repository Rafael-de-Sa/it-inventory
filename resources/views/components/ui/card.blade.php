{{-- Card centralizado usado em telas de cadastro, edição e visualização. --}}
@props(['size' => 'md'])

<div class="flex w-full justify-center">
    <div {{ $attributes->class([
        'w-full space-y-6 rounded-2xl border border-green-800 bg-green-900/40 p-6 shadow-lg md:p-8',
        'max-w-lg' => $size === 'sm',
        'max-w-3xl' => $size === 'md',
        'max-w-4xl' => $size === 'lg',
        'max-w-7xl' => $size === 'xl',
    ]) }}>
        {{ $slot }}
    </div>
</div>
