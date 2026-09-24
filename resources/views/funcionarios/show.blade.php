@extends('layouts.main_layout')

@section('content')
    @php
        $empresa = $funcionario->setor?->empresa;
        $jaDesligado = $restricoesDesligamento['ja_desligado'] ?? false;
    @endphp

    <x-ui.card size="lg">
        <x-ui.card-header :title="'Funcionário — ' . ($funcionario->nome_completo ?: '—')">
            <x-slot:subtitle>
                <x-ui.timestamps :model="$funcionario" />
            </x-slot:subtitle>
        </x-ui.card-header>

        <x-form.grid>
            <x-form.readonly label="ID" :value="$funcionario->id" wrapper-class="md:col-span-3" />
            <x-form.active-toggle id="funcionario_ativo" :checked="$funcionario->ativo" disabled
                wrapper-class="md:col-span-3" />
            <x-form.readonly label="Matrícula" :value="$funcionario->matricula" wrapper-class="md:col-span-3" />
            <x-form.active-toggle id="funcionario_terceirizado" label="Terceirizado" active-label="Sim"
                inactive-label="Não" :checked="$funcionario->terceirizado" disabled wrapper-class="md:col-span-3" />

            <x-form.readonly label="Admitido em" :value="$funcionario->admitido_em?->format('d/m/Y')"
                wrapper-class="md:col-span-3" />
            <x-form.readonly label="Desligado em" :value="$funcionario->desligado_em?->format('d/m/Y')"
                wrapper-class="md:col-span-3" />
            <x-form.readonly label="Nome" :value="$funcionario->nome" wrapper-class="md:col-span-6" />

            <x-form.readonly label="Sobrenome" :value="$funcionario->sobrenome" wrapper-class="md:col-span-6" />
            <x-form.readonly label="CPF" :value="\App\Support\Mask::cpf($funcionario->cpf)"
                wrapper-class="md:col-span-3" />
            <x-form.readonly label="Telefone" :value="\App\Support\Mask::telefone($funcionario->telefone)"
                wrapper-class="md:col-span-3" />

            <x-form.readonly label="Setor" :value="$funcionario->setor?->nome" wrapper-class="md:col-span-4" />
            <x-form.readonly label="Empresa" :value="$empresa?->nome_fantasia" wrapper-class="md:col-span-5" />
            <x-form.readonly label="CNPJ da Empresa" :value="\App\Support\Mask::cnpj($empresa?->cnpj)"
                wrapper-class="md:col-span-3" />
        </x-form.grid>

        <div class="space-y-3 border-t border-line pt-5">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <x-ui.button :href="route('funcionarios.index')" icon="fa-solid fa-arrow-left" class="text-sm">
                    Voltar
                </x-ui.button>

                <div class="flex flex-wrap items-center gap-2 md:gap-3">
                    <x-ui.button :href="route('relatorios.funcionarios.equipamentos', $funcionario)" target="_blank"
                        rel="noopener noreferrer" icon="fa-solid fa-file-pdf" class="text-sm">
                        Relatório de equipamentos
                    </x-ui.button>

                    @if ($podeMostrarBotaoDesligar)
                        <x-ui.button type="button" variant="warning" icon="fa-solid fa-user-slash" class="text-sm"
                            onclick="document.getElementById('modal-desligamento').showModal()">
                            Registrar desligamento
                        </x-ui.button>
                    @endif

                    <x-ui.button :href="route('funcionarios.edit', $funcionario)" icon="fa-solid fa-pen-to-square"
                        class="text-sm">
                        Editar
                    </x-ui.button>

                    @if ($podeMostrarBotaoExcluir)
                        <x-ui.delete-button :action="route('funcionarios.destroy', $funcionario)"
                            confirm="Excluir o funcionário?" class="text-sm" />
                    @endif
                </div>
            </div>

            {{-- Já desligado: situação informativa, não é pendência --}}
            @if ($jaDesligado)
                <x-ui.alert variant="neutral" icon="fa-solid fa-user-slash"
                    :title="'Funcionário desligado em ' . $funcionario->desligado_em->format('d/m/Y') . '.'">
                    <p>Ações de desligamento e exclusão não estão mais disponíveis.</p>
                </x-ui.alert>
            @endif

            @if (!$jaDesligado && !$podeRealizarDesligamento && !$funcionarioPertenceAoUsuarioLogado)
                <x-ui.alert variant="warning" icon="fa-solid fa-circle-exclamation"
                    title="Ações de desligamento e exclusão indisponíveis devido a pendências:">
                    <ul class="list-inside list-disc space-y-0.5">
                        @if ($restricoesDesligamento['equipamentos_em_uso'] ?? false)
                            <li>Há equipamentos sob responsabilidade deste funcionário.</li>
                        @endif
                        @if ($restricoesDesligamento['termos_responsabilidade_pendentes'] ?? false)
                            <li>Existem termos de responsabilidade pendentes de upload.</li>
                        @endif
                        @if ($restricoesDesligamento['termos_devolucao_pendentes'] ?? false)
                            <li>Existem termos de devolução pendentes de upload.</li>
                        @endif
                    </ul>
                    @cannot('gerenciar-movimentacoes')
                        <p class="pt-1 font-medium">
                            Encaminhe à equipe de TIC para a devolução dos equipamentos e a regularização dos termos.
                        </p>
                    @endcannot
                </x-ui.alert>
            @endif

            @if ($funcionarioPertenceAoUsuarioLogado)
                <x-ui.alert variant="info" icon="fa-solid fa-circle-info"
                    title="Você não pode realizar o próprio desligamento ou exclusão de usuário." />
            @endif
        </div>
    </x-ui.card>

    @if ($podeMostrarBotaoDesligar)
        <x-ui.modal id="modal-desligamento" title="Registrar desligamento" :open="$errors->has('desligado_em')">
            @if ($funcionario->admitido_em)
                <form method="POST" action="{{ route('funcionarios.desligar', $funcionario) }}" class="space-y-5">
                    @csrf

                    <p class="text-sm text-ink-muted">
                        O funcionário será marcado como inativo e o usuário vinculado a ele será removido.
                    </p>

                    <x-form.input type="date" name="desligado_em" label="Data de desligamento" required
                        :value="today()->toDateString()" :min="$funcionario->admitido_em->toDateString()"
                        :max="today()->toDateString()"
                        :help="'Entre a admissão (' . $funcionario->admitido_em->format('d/m/Y') . ') e hoje.'" />

                    <x-form.actions>
                        <x-ui.button type="button" onclick="this.closest('dialog').close()">Cancelar</x-ui.button>
                        <x-ui.button variant="warning" icon="fa-solid fa-user-slash">Confirmar desligamento</x-ui.button>
                    </x-form.actions>
                </form>
            @else
                <x-ui.alert variant="warning" class="text-sm">
                    Este funcionário não tem data de admissão. Informe-a no cadastro antes de registrar o desligamento.
                </x-ui.alert>

                <x-form.actions>
                    <x-ui.button type="button" onclick="this.closest('dialog').close()">Fechar</x-ui.button>
                    <x-ui.button :href="route('funcionarios.edit', $funcionario)" variant="primary"
                        icon="fa-solid fa-pen-to-square">Editar cadastro</x-ui.button>
                </x-form.actions>
            @endif
        </x-ui.modal>
    @endif
@endsection
