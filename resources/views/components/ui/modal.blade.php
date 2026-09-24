{{--
    Modal com <dialog> nativo (fecha com Esc, foco preso no modal, fundo escurecido).
    Abrir:  onclick="document.getElementById('meu-modal').showModal()"
    Fechar: qualquer botão com onclick="this.closest('dialog').close()"
    `open` abre automaticamente ao carregar (ex.: quando voltou com erro de validação).
--}}
@props([
    'id',
    'title',
    'open' => false,
])

<dialog id="{{ $id }}" aria-labelledby="{{ $id }}-title" {{ $attributes->class(
    'm-auto w-[calc(100%-2rem)] max-w-md rounded-xl border border-line bg-surface text-ink shadow-xl backdrop:bg-zinc-900/40 backdrop:backdrop-blur-[2px]'
) }}>
    <div class="space-y-5 p-6">
        <header class="flex items-start justify-between gap-3">
            <h3 id="{{ $id }}-title" class="text-lg font-semibold tracking-tight">{{ $title }}</h3>
            <button type="button" class="-m-1 cursor-pointer rounded-md p-1 text-ink-subtle hover:bg-surface-muted hover:text-ink"
                aria-label="Fechar" onclick="this.closest('dialog').close()">✕</button>
        </header>

        {{ $slot }}
    </div>
</dialog>

@if ($open)
    <script>
        document.getElementById({{ Js::from($id) }})?.showModal();
    </script>
@endif
