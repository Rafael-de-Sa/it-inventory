{{-- Dashboard do Departamento Pessoal. Dados: App\Services\PainelInicial::dp() --}}
<section class="space-y-5" aria-label="Painel do Departamento Pessoal">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-ui.stat label="Funcionários ativos" :value="$painel['funcionariosAtivos']" icon="fa-solid fa-user-tie"
            :href="route('funcionarios.index')" tone="success" />
        <x-ui.stat label="Com equipamentos" :value="$painel['funcionariosComEquipamento']" icon="fa-solid fa-laptop" />
        <x-ui.stat label="Termos aguardando assinatura" :value="$painel['termosPendentes']" icon="fa-solid fa-file-signature"
            :tone="$painel['termosPendentes'] ? 'warning' : 'neutral'" />
        <x-ui.stat label="Desligados com pendência" :value="$painel['desligadosComPendencia']" icon="fa-solid fa-user-xmark"
            :tone="$painel['desligadosComPendencia'] ? 'danger' : 'neutral'"
            :hint="$painel['desligadosComPendencia'] ? 'Encaminhe à TI para a devolução' : 'Nenhuma pendência'" />
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

    <x-table title="Funcionários com pendências" hint="Equipamentos em uso ou termos aguardando assinatura"
        :headers="['Funcionário', 'Setor', 'Situação', 'Pendências', '']">
        @forelse ($painel['pendencias'] as $funcionario)
            @php
                $pendencias = array_filter([
                    $funcionario->possui_equipamentos_em_uso ? 'Equipamentos em uso' : null,
                    $funcionario->possui_termos_responsabilidade_pendentes ? 'Termo de responsabilidade' : null,
                    $funcionario->possui_termos_devolucao_pendentes ? 'Termo de devolução' : null,
                ]);
            @endphp
            <x-table.row>
                <x-table.cell class="text-left">
                    {{ $funcionario->nome_completo }}@if ($funcionario->matricula) <span class="text-ink-muted">({{ $funcionario->matricula }})</span>@endif
                </x-table.cell>
                <x-table.cell>{{ $funcionario->setor?->nome ?? '—' }}</x-table.cell>
                <x-table.cell>
                    <x-ui.badge :tone="$funcionario->desligado_em ? 'danger' : 'neutral'">
                        {{ $funcionario->desligado_em ? 'Desligado em ' . $funcionario->desligado_em->format('d/m/Y') : 'Ativo' }}
                    </x-ui.badge>
                </x-table.cell>
                <x-table.cell class="text-left">{{ implode(', ', $pendencias) }}</x-table.cell>
                <x-table.actions :show="route('funcionarios.show', $funcionario)" />
            </x-table.row>
        @empty
            <x-table.empty :colspan="5">Nenhum funcionário com pendência.</x-table.empty>
        @endforelse
    </x-table>
</section>
