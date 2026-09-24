@extends('layouts.main_layout')

@section('content')
    <x-ui.page title="Ocorrências">
        <x-slot:actions>
            <x-ui.button :href="route('ocorrencias.create')" variant="soft" icon="fa-solid fa-plus" class="text-sm">
                Registrar
            </x-ui.button>
        </x-slot:actions>

        <x-form.filters :reset="route('ocorrencias.index')">
            <x-form.select name="situacao" label="Situação" :value="$situacao"
                :options="['abertas' => 'Abertas', 'resolvidas' => 'Resolvidas', 'todas' => 'Todas']"
                wrapper-class="md:col-span-3" />

            <x-form.input name="busca" label="Busca" :value="$busca"
                placeholder="Nº, problema, protocolo, identificação, S/N, patrimônio ou modelo…" wrapper-class="md:col-span-9" />
        </x-form.filters>

        <x-table :headers="['Nº', 'Reportado em', 'Equipamento', 'Problema', 'Chamado', 'Previsão / Liberação', 'Situação', 'Ações']">
            @forelse ($ocorrencias as $ocorrencia)
                @php $equipamento = $ocorrencia->equipamento; @endphp
                <x-table.row>
                    <x-table.cell>{{ $ocorrencia->id }}</x-table.cell>
                    <x-table.cell>{{ $ocorrencia->reportado_em->format('d/m/Y') }}</x-table.cell>
                    <x-table.cell class="text-left">
                        {{ $equipamento?->tipoEquipamento?->nome }} {{ $equipamento?->nome_exibicao }}
                        @if ($equipamento?->identificacao)
                            <span class="block text-xs text-ink-muted">{{ $equipamento->identificacao }}</span>
                        @endif
                    </x-table.cell>
                    <x-table.cell class="max-w-72 text-left">{{ $ocorrencia->problema }}</x-table.cell>
                    <x-table.cell>{{ $ocorrencia->chamado ?? '—' }}</x-table.cell>
                    <x-table.cell>
                        @if ($ocorrencia->liberado_em)
                            Liberado {{ $ocorrencia->liberado_em->format('d/m/Y') }}
                        @elseif ($ocorrencia->previsao_em)
                            Previsão {{ $ocorrencia->previsao_em->format('d/m/Y') }}
                        @else
                            <x-ui.muted>Sem previsão</x-ui.muted>
                        @endif
                    </x-table.cell>
                    <x-table.cell>
                        <x-ui.badge :tone="$ocorrencia->estaAberta() ? 'warning' : 'success'">{{ $ocorrencia->situacao_rotulo }}</x-ui.badge>
                    </x-table.cell>
                    <x-table.actions :show="route('ocorrencias.show', $ocorrencia)" :edit="route('ocorrencias.edit', $ocorrencia)" />
                </x-table.row>
            @empty
                <x-table.empty :colspan="8">Nenhuma ocorrência encontrada.</x-table.empty>
            @endforelse
        </x-table>

        <div>
            {{ $ocorrencias->onEachSide(1)->links() }}
        </div>
    </x-ui.page>
@endsection
