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
    'm-auto w-[calc(100%-2rem)] max-w-md rounded-2xl border border-green-800 bg-green-950 text-white shadow-xl backdrop:bg-black/60'
) }}>
    <div class="space-y-5 p-6">
        <header class="flex items-start justify-between gap-3">
            <h3 id="{{ $id }}-title" class="text-lg font-semibold tracking-wide">{{ $title }}</h3>
            <button type="button" class="cursor-pointer hover:opacity-75" aria-label="Fechar"
                onclick="this.closest('dialog').close()">✕</button>
        </header>

        {{ $slot }}
    </div>
</dialog>

@if ($open)
    <script>
        document.getElementById({{ Js::from($id) }})?.showModal();
    </script>
@endif
