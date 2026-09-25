{{-- Dashboard da TIC. Dados: App\Services\PainelInicial::tic() --}}
@use('App\Models\Ocorrencia')
@use('App\Services\IndicadoresManutencao')

@php
    $corStatus = [
        'disponivel' => 'bg-brand-500',
        'em_uso' => 'bg-sky-500',
        'em_manutencao' => 'bg-amber-500',
        'defeituoso' => 'bg-red-500',
        'descartado' => 'bg-zinc-400',
        'baixado' => 'bg-zinc-500',
    ];
    $rotuloEquipamento = fn ($equipamento) => trim('#' . $equipamento->id . ' ' . ($equipamento->tipoEquipamento?->nome ?? '') . ' ' . $equipamento->nome_exibicao)
        . ($equipamento->identificacao ? ' · ' . $equipamento->identificacao : '');
@endphp

<section class="space-y-5" aria-label="Painel da TIC">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-ui.stat label="Equipamentos no parque" :value="$painel['totalEquipamentos']" icon="fa-solid fa-computer"
            :href="route('equipamentos.index')" tone="success"
            :hint="($painel['porStatus']->firstWhere('status', 'disponivel')['total'] ?? 0) . ' disponíveis'" />
        <x-ui.stat label="Ocorrências abertas" :value="$painel['ocorrenciasAbertas']" icon="fa-solid fa-triangle-exclamation"
            :href="route('ocorrencias.index')" :tone="$painel['ocorrenciasAtrasadas'] ? 'danger' : 'warning'"
            :hint="$painel['ocorrenciasAtrasadas'] ? $painel['ocorrenciasAtrasadas'] . ' com previsão vencida' : 'Nenhuma atrasada'" />
        <x-ui.stat label="Termos aguardando assinatura" :value="$painel['termosPendentes']" icon="fa-solid fa-file-signature"
            :href="route('movimentacoes.index', ['status' => 'pendente'])" />
        <x-ui.stat :label="'Custo de manutenção em ' . now()->year" :value="Ocorrencia::reais($painel['custoAno'])"
            icon="fa-solid fa-screwdriver-wrench"
            :hint="$painel['resolucaoMediaDias'] !== null ? 'Resolução média: ' . str_replace('.', ',', $painel['resolucaoMediaDias']) . ' dia(s)' : null" />
    </div>

    @if ($painel['atalhos'])
        <div class="rounded-xl border border-line bg-surface p-4 shadow-sm">
            <h2 class="mb-3 text-sm font-semibold text-ink">Atalhos</h2>
            {{-- No celular cada atalho ocupa a linha toda; a partir de sm ficam lado a lado. --}}
            <div class="grid grid-cols-1 gap-2 sm:flex sm:flex-wrap">
                @foreach ($painel['atalhos'] as $atalho)
                    <x-ui.button :href="route($atalho['rota'])" variant="soft" :icon="$atalho['icone']"
                        class="w-full justify-center text-sm sm:w-auto">{{ $atalho['rotulo'] }}</x-ui.button>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
        <div class="rounded-xl border border-line bg-surface p-4 shadow-sm">
            <h2 class="mb-3 text-sm font-semibold text-ink">Parque por situação</h2>
            <ul class="space-y-2.5">
                @foreach ($painel['porStatus'] as $linha)
                    @php $percentual = $painel['totalEquipamentos'] ? round($linha['total'] / $painel['totalEquipamentos'] * 100) : 0; @endphp
                    <li>
                        <a href="{{ route('equipamentos.index', ['status' => $linha['status']]) }}" class="group block">
                            <span class="flex justify-between text-sm">
                                <span class="text-ink group-hover:underline">{{ $linha['rotulo'] }}</span>
                                <span class="text-ink-muted">{{ $linha['total'] }}</span>
                            </span>
                            <span class="mt-1 block h-2 overflow-hidden rounded-full bg-surface-muted" aria-hidden="true">
                                <span class="block h-full rounded-full {{ $corStatus[$linha['status']] ?? 'bg-zinc-400' }}" style="width: {{ $percentual }}%"></span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="rounded-xl border border-line bg-surface p-4 shadow-sm">
            <h2 class="mb-3 text-sm font-semibold text-ink">Disponíveis para entrega, por tipo</h2>
            @forelse ($painel['disponiveisPorTipo'] as $tipo => $total)
                <div class="flex justify-between border-b border-line py-2 text-sm last:border-b-0">
                    <span class="text-ink">{{ $tipo }}</span>
                    <span class="font-medium text-ink">{{ $total }}</span>
                </div>
            @empty
                <p class="text-sm text-ink-muted">Nenhum equipamento disponível no momento.</p>
            @endforelse
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
        <x-table title="Ocorrências abertas há mais tempo" :headers="['Nº', 'Desde', 'Equipamento', 'Problema']">
            @forelse ($painel['ocorrenciasMaisAntigas'] as $ocorrencia)
                <x-table.row>
                    <x-table.cell><a href="{{ route('ocorrencias.show', $ocorrencia) }}" class="hover:underline">#{{ $ocorrencia->id }}</a></x-table.cell>
                    <x-table.cell>{{ $ocorrencia->reportado_em->format('d/m/Y') }}</x-table.cell>
                    <x-table.cell class="text-left">{{ $ocorrencia->equipamento ? $rotuloEquipamento($ocorrencia->equipamento) : '—' }}</x-table.cell>
                    <x-table.cell class="text-left">{{ $ocorrencia->problema }}</x-table.cell>
                </x-table.row>
            @empty
                <x-table.empty :colspan="4">Nenhuma ocorrência aberta.</x-table.empty>
            @endforelse
        </x-table>

        <x-table title="Últimas movimentações" :headers="['Nº', 'Data', 'Tipo', 'Funcionário']">
            @forelse ($painel['ultimasMovimentacoes'] as $movimentacao)
                <x-table.row>
                    <x-table.cell><a href="{{ route('movimentacoes.show', $movimentacao) }}" class="hover:underline">#{{ $movimentacao->id }}</a></x-table.cell>
                    <x-table.cell>{{ $movimentacao->criado_em?->format('d/m/Y') }}</x-table.cell>
                    <x-table.cell>{{ $movimentacao->tipo_rotulo }}</x-table.cell>
                    <x-table.cell class="text-left">{{ $movimentacao->funcionario?->nome_completo ?? '—' }}</x-table.cell>
                </x-table.row>
            @empty
                <x-table.empty :colspan="4">Nenhuma movimentação registrada.</x-table.empty>
            @endforelse
        </x-table>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
        <x-table title="Maior custo de manutenção" :headers="['Equipamento', 'Ocorrências', 'Custo', '% da compra']">
            @forelse ($painel['maiorCusto'] as $linha)
                @php $valorCompra = (float) $linha['equipamento']->valor_compra; @endphp
                <x-table.row>
                    <x-table.cell class="text-left">
                        <a href="{{ route('equipamentos.show', $linha['equipamento']) }}" class="hover:underline">{{ $rotuloEquipamento($linha['equipamento']) }}</a>
                    </x-table.cell>
                    <x-table.cell>{{ $linha['quantidade'] }}</x-table.cell>
                    <x-table.cell>{{ Ocorrencia::reais($linha['custo']) }}</x-table.cell>
                    <x-table.cell>{{ $valorCompra > 0 ? IndicadoresManutencao::percentual(round($linha['custo'] / $valorCompra * 100, 1)) : '—' }}</x-table.cell>
                </x-table.row>
            @empty
                <x-table.empty :colspan="4">Nenhum custo de manutenção registrado.</x-table.empty>
            @endforelse
        </x-table>

        <x-table title="Menor disponibilidade (uptime)" :headers="['Equipamento', 'Tempo parado', 'Disponibilidade']">
            @forelse ($painel['menorDisponibilidade'] as $linha)
                <x-table.row>
                    <x-table.cell class="text-left">
                        <a href="{{ route('equipamentos.show', $linha['equipamento']) }}" class="hover:underline">{{ $rotuloEquipamento($linha['equipamento']) }}</a>
                    </x-table.cell>
                    <x-table.cell>{{ IndicadoresManutencao::duracao($linha['segundos_parado']) }}</x-table.cell>
                    <x-table.cell>{{ IndicadoresManutencao::percentual($linha['disponibilidade']) }}</x-table.cell>
                </x-table.row>
            @empty
                <x-table.empty :colspan="3">Nenhum equipamento ficou parado.</x-table.empty>
            @endforelse
        </x-table>
    </div>
</section>
