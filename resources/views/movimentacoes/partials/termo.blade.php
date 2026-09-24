{{--
    Seção do termo (responsabilidade ou devolução): gerar PDF, enviar assinado ou visualizar o já enviado.
    Variáveis: $titulo, $enviado (bool), $rotaGerar, $rotaUpload, $rotaVisualizar, $formId,
               $textoUpload, $rotuloGerar, $rotuloUpload, $rotuloVisualizar, $tituloEnviado.
--}}
<section class="space-y-4">
    <h3 class="text-lg font-semibold tracking-tight text-ink">{{ $titulo }}</h3>

    <div class="space-y-6 rounded-xl border border-line bg-surface-muted p-6">
        @if (!$enviado)
            <div class="grid gap-6 md:grid-cols-2 md:items-start">
                <form id="{{ $formId }}" method="POST" action="{{ $rotaUpload }}" enctype="multipart/form-data">
                    @csrf
                    <x-form.file name="arquivo_termo" label="Upload do termo assinado (PDF)" accept="application/pdf"
                        :help="$textoUpload" />
                </form>

                <div class="flex flex-col items-stretch gap-3 md:items-end">
                    <x-ui.button :href="$rotaGerar" target="_blank" rel="noopener noreferrer" variant="soft"
                        icon="fa-solid fa-file-pdf" class="justify-center text-sm">{{ $rotuloGerar }}</x-ui.button>
                    <x-ui.button form="{{ $formId }}" variant="primary" icon="fa-solid fa-cloud-arrow-up"
                        class="justify-center text-sm">{{ $rotuloUpload }}</x-ui.button>
                </div>
            </div>
        @else
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="space-y-1">
                    <p class="text-sm font-medium text-ink">{{ $tituloEnviado }}</p>
                    <p class="text-sm text-ink-muted">
                        Já existe um termo enviado para esta movimentação. Não é permitido enviar um novo arquivo.
                    </p>
                </div>
                <x-ui.button :href="$rotaVisualizar" target="_blank" rel="noopener noreferrer" variant="soft"
                    icon="fa-solid fa-eye" class="justify-center text-sm">{{ $rotuloVisualizar }}</x-ui.button>
            </div>
        @endif

        <div class="border-t border-line pt-4">
            <x-ui.button :href="route('movimentacoes.index')" icon="fa-solid fa-arrow-left" class="text-sm">Voltar</x-ui.button>
        </div>
    </div>
</section>
