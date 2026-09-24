@extends('layouts.main_layout')

@section('content')
    <x-ui.card size="lg">
        <x-ui.card-header :title="'Movimentação — #' . $movimentacao->id"
            :subtitle="'Data da movimentação: ' . ($movimentacao->criado_em?->format('d/m/Y H:i') ?? '—')" />

        @include('movimentacoes.partials.dados', ['rotuloObservacao' => 'Observações da movimentação'])

        <section class="space-y-3">
            <x-table title="Equipamentos desta movimentação"
                :headers="['Patrimônio', 'Tipo', 'Descrição', 'Número de série', 'Motivo da devolução', 'Observação da devolução']">
                @forelse ($movimentacao->equipamentos as $equipamento)
                    <x-table.row>
                        <x-table.cell>{{ $equipamento->patrimonio ?? '—' }}</x-table.cell>
                        <x-table.cell>{{ $equipamento->tipoEquipamento->nome ?? '—' }}</x-table.cell>
                        <x-table.cell>{{ $equipamento->nome_exibicao ?: '—' }}</x-table.cell>
                        <x-table.cell>{{ $equipamento->numero_serie ?? '—' }}</x-table.cell>
                        <x-table.cell>
                            {{ \App\Models\MovimentacaoEquipamento::MOTIVOS_DEVOLUCAO[$equipamento->pivot->motivo_devolucao] ?? '—' }}
                        </x-table.cell>
                        <x-table.cell>{{ $equipamento->pivot->observacao ?? '—' }}</x-table.cell>
                    </x-table.row>
                @empty
                    <x-table.empty :colspan="6">Nenhum equipamento vinculado a esta movimentação.</x-table.empty>
                @endforelse
            </x-table>
        </section>

        @include('movimentacoes.partials.termo', [
            'titulo' => 'Termo de devolução',
            'enviado' => filled($movimentacao->termo_devolucao),
            'formId' => 'form-upload-termo-devolucao',
            'rotaGerar' => route('movimentacoes.termo-devolucao', $movimentacao),
            'rotaUpload' => route('movimentacoes.upload-termo-devolucao', $movimentacao),
            // ?v= evita que o navegador mostre uma versão em cache do PDF.
            'rotaVisualizar' => route('movimentacoes.termo.devolucao.visualizar', $movimentacao) . '?v=' . ($movimentacao->atualizado_em?->timestamp ?? now()->timestamp),
            'textoUpload' => 'Envie o termo de devolução assinado para manter o registro associado a esta movimentação.',
            'rotuloGerar' => 'Gerar termo de devolução',
            'rotuloUpload' => 'Upload termo de devolução',
            'rotuloVisualizar' => 'Visualizar termo de devolução',
            'tituloEnviado' => 'Termo de devolução armazenado',
        ])
    </x-ui.card>
@endsection
