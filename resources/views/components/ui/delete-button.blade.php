{{-- Form de exclusão (DELETE) com confirmação. Use `icon-only` dentro de tabelas. --}}
@props([
    'action',
    'confirm' => 'Tem certeza que deseja excluir este registro?',
    'label' => 'Excluir',
    'iconOnly' => false,
])

<form method="POST" action="{{ $action }}" class="inline" onsubmit="return confirm({{ Js::from($confirm) }});">
    @csrf
    @method('DELETE')

    @if ($iconOnly)
        <x-ui.icon-button icon="fa-solid fa-trash" :label="$label" variant="danger" {{ $attributes }} />
    @else
        <x-ui.button variant="danger" icon="fa-solid fa-trash" {{ $attributes }}>{{ $label }}</x-ui.button>
    @endif
</form>
