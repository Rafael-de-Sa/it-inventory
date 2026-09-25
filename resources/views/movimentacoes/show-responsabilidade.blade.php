@extends('layouts.main_layout')

@section('content')
    @php
        // Link "Devolver" já abre a devolução com empresa, setor e funcionário selecionados.
        $urlDevolucao = route('movimentacoes.devolucao.create', [
            'empresa_id' => $movimentacao->setor?->empresa_id,
            'setor_id' => $movimentacao->setor_id,
            'funcionario_id' => $movimentacao->funcionario_id,
        ]);
    @endphp

    <x-ui.card size="lg">
        <x-ui.card-header :title="'Movimentação — #' . $movimentacao->id"
            :subtitle="'Data da movimentação: ' . ($movimentacao->criado_em?->format('d/m/Y H:i') ?? '—')" />

        @include('movimentacoes.partials.dados')

        <section class="space-y-3">
            <x-table title="Equipamentos desta movimentação"
                :headers="['ID', 'Descrição', 'Patrimônio', 'Nº Série', 'Status', 'Ação']">
                @forelse ($movimentacao->equipamentos as $equipamento)
                    @php $estaDevolvido = filled($equipamento->pivot->devolvido_em); @endphp
                    <x-table.row>
                        <x-table.cell>{{ $equipamento->id }}</x-table.cell>
                        <x-table.cell>{{ $equipamento->nome_exibicao ?: '-' }}</x-table.cell>
                        <x-table.cell>{{ $equipamento->patrimonio ?? '-' }}</x-table.cell>
                        <x-table.cell>{{ $equipamento->numero_serie ?? '-' }}</x-table.cell>
                        <x-table.cell>
                            <x-ui.badge :tone="$estaDevolvido ? 'success' : 'info'">
                                {{ $estaDevolvido ? 'Devolvido' : 'Em uso' }}
                            </x-ui.badge>
                        </x-table.cell>
                        <x-table.cell>
                            @if ($estaDevolvido)
                                <x-ui.muted>—</x-ui.muted>
                            @else
                                <x-ui.button :href="$urlDevolucao" variant="soft" icon="fa-solid fa-rotate-left"
                                    class="px-3 py-1.5 text-xs">Devolver</x-ui.button>
                            @endif
                        </x-table.cell>
                    </x-table.row>
                @empty
                    <x-table.empty :colspan="6">Nenhum equipamento vinculado a esta movimentação.</x-table.empty>
                @endforelse
            </x-table>
        </section>

        @include('movimentacoes.partials.termo', [
            'titulo' => 'Termo de responsabilidade',
            'enviado' => filled($movimentacao->termo_responsabilidade),
            'formId' => 'form-upload-termo',
            'rotaGerar' => route('movimentacoes.termo-responsabilidade', $movimentacao),
            'rotaUpload' => route('movimentacoes.upload-termo-responsabilidade', $movimentacao),
            'rotaVisualizar' => route('movimentacoes.termo.responsabilidade.visualizar', $movimentacao),
            'textoUpload' => 'Envie o termo assinado para concluir a movimentação.',
            'rotuloGerar' => 'Gerar termo de responsabilidade',
            'rotuloUpload' => 'Upload termo de responsabilidade',
            'rotuloVisualizar' => 'Visualizar termo',
            'tituloEnviado' => 'Termo de responsabilidade',
        ])
    </x-ui.card>
@endsection
