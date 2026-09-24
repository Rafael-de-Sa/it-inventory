@extends('layouts.main_layout')

@use('App\Http\Controllers\TrocaController')

@section('content')
    @php
        $pares = TrocaController::paresDaTroca($movimentacao);
        $rotulo = fn ($equipamento) => $equipamento
            ? trim('#' . $equipamento->id . ' · ' . ($equipamento->tipoEquipamento?->nome ?? '') . ' ' . $equipamento->nome_exibicao)
            : '—';
    @endphp

    <x-ui.card size="lg">
        <x-ui.card-header :title="'Troca — #' . $movimentacao->id"
            :subtitle="'Data da troca: ' . ($movimentacao->criado_em?->format('d/m/Y H:i') ?? '—') . ' · ' . $movimentacao->tipo_troca_rotulo" />

        @include('movimentacoes.partials.dados')

        @foreach ($movimentacao->ocorrencias as $ocorrencia)
            <x-ui.alert icon="fa-solid fa-triangle-exclamation">
                Troca registrada para a
                <a href="{{ route('ocorrencias.show', $ocorrencia) }}" class="font-medium underline">ocorrência #{{ $ocorrencia->id }}</a>:
                {{ $ocorrencia->problema }}
            </x-ui.alert>
        @endforeach

        <section class="space-y-3">
            <x-table title="Equipamentos trocados"
                :headers="['Devolvido', 'Condição', 'Entregue', 'Identificação', 'Situação do entregue']">
                @forelse ($pares as $par)
                    @php
                        $devolvido = $par['devolvido'];
                        $entregue = $par['entregue'];
                        $entregueDevolvido = filled($entregue->pivot->devolvido_em);
                    @endphp
                    <x-table.row>
                        <x-table.cell class="text-left">
                            @if ($devolvido?->equipamento)
                                <a href="{{ route('equipamentos.show', $devolvido->equipamento) }}" class="hover:underline">{{ $rotulo($devolvido->equipamento) }}</a>
                            @else
                                —
                            @endif
                        </x-table.cell>
                        <x-table.cell>{{ $devolvido?->motivo_devolucao_rotulo ?? '—' }}</x-table.cell>
                        <x-table.cell class="text-left">
                            <a href="{{ route('equipamentos.show', $entregue) }}" class="hover:underline">{{ $rotulo($entregue) }}</a>
                        </x-table.cell>
                        <x-table.cell>{{ $entregue->identificacao ?? '—' }}</x-table.cell>
                        <x-table.cell>
                            <x-ui.badge :tone="$entregueDevolvido ? 'success' : 'info'">
                                {{ $entregueDevolvido ? 'Devolvido' : 'Em uso' }}
                            </x-ui.badge>
                        </x-table.cell>
                    </x-table.row>
                @empty
                    <x-table.empty :colspan="5">Nenhum equipamento vinculado a esta troca.</x-table.empty>
                @endforelse
            </x-table>
        </section>

        @include('movimentacoes.partials.termo', [
            'titulo' => 'Termo de troca',
            'enviado' => filled($movimentacao->termo_responsabilidade),
            'formId' => 'form-upload-termo',
            'rotaGerar' => route('movimentacoes.termo-troca', $movimentacao),
            'rotaUpload' => route('movimentacoes.upload-termo-responsabilidade', $movimentacao),
            'rotaVisualizar' => route('movimentacoes.termo.responsabilidade.visualizar', $movimentacao),
            'textoUpload' => 'Envie o termo de troca assinado para concluir a movimentação.',
            'rotuloGerar' => 'Gerar termo de troca',
            'rotuloUpload' => 'Upload termo de troca',
            'rotuloVisualizar' => 'Visualizar termo',
            'tituloEnviado' => 'Termo de troca',
        ])
    </x-ui.card>
@endsection
