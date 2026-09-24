{{--
    Invólucro de campo: label + controle (slot) + mensagem de erro ou ajuda.
    Normalmente não é usado direto — input/select/textarea já o utilizam.
--}}
@props([
    'label' => null,
    'for' => null,
    'errorKey' => null,
    'help' => null,
    'required' => false,
])

<div {{ $attributes }}>
    @if ($label)
        <label @if ($for) for="{{ $for }}" @endif class="mb-1.5 block text-sm font-medium text-ink">
            {{ $label }}@if ($required)<span class="text-red-600" aria-hidden="true">*</span>@endif
        </label>
    @endif

    {{ $slot }}

    @if ($errorKey && $errors->has($errorKey))
        <p @if ($for) id="{{ $for }}_help" @endif class="mt-1.5 flex items-center gap-1 text-xs font-medium text-red-700">
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
            </svg>
            {{ $errors->first($errorKey) }}
        </p>
    @elseif (filled((string) $help))
        <p @if ($for) id="{{ $for }}_help" @endif class="mt-1.5 text-xs text-ink-muted">{{ $help }}</p>
    @endif
</div>
