@extends('layouts.main_layout')

@section('content')
    @php
        $equipamento = $ocorrencia->equipamento;
        $funcionario = $ocorrencia->funcionario;
        $data = fn ($valor) => $valor?->format('d/m/Y');
    @endphp

    <x-ui.card size="lg">
        <x-ui.card-header :title="'Ocorrência — #' . $ocorrencia->id">
            <x-slot:subtitle>
                <x-ui.badge :tone="$ocorrencia->estaAberta() ? 'warning' : 'success'">{{ $ocorrencia->situacao_rotulo }}</x-ui.badge>
                <span class="ml-2">
                    Registrada em {{ $ocorrencia->criado_em?->format('d/m/Y H:i') }}
                    @if ($ocorrencia->usuario)
                        por {{ $ocorrencia->usuario->funcionario?->nome_completo ?? $ocorrencia->usuario->email }}
                    @endif
                </span>
            </x-slot:subtitle>
        </x-ui.card-header>

        @if ($ocorrencia->troca)
            <x-ui.alert icon="fa-solid fa-right-left">
                Resolvida com a
                <a href="{{ route('movimentacoes.show', $ocorrencia->troca) }}" class="font-medium underline">troca #{{ $ocorrencia->troca->id }}</a>
                ({{ \Illuminate\Support\Str::lower($ocorrencia->troca->tipo_troca_rotulo) }}).
            </x-ui.alert>
        @endif

        <x-form.fieldset legend="Equipamento">
            <x-form.grid>
                <x-form.field label="Equipamento" class="md:col-span-8">
                    <div class="flex min-h-10 items-center text-sm">
                        @if ($equipamento)
                            <a href="{{ route('equipamentos.show', $equipamento) }}" class="font-medium text-ink hover:underline">
                                #{{ $equipamento->id }} · {{ $equipamento->tipoEquipamento?->nome }} {{ $equipamento->nome_exibicao }}
                            </a>
                            @if ($equipamento->identificacao)
                                <span class="ml-2 text-ink-muted">{{ $equipamento->identificacao }}</span>
                            @endif
                        @else
                            —
                        @endif
                    </div>
                </x-form.field>
                <x-form.readonly label="Status atual" :value="$equipamento?->status_rotulo" wrapper-class="md:col-span-4" />
                <x-form.readonly label="Último usuário"
                    :value="$funcionario ? $funcionario->nome_completo . ($funcionario->matricula ? ' (' . $funcionario->matricula . ')' : '') : null"
                    wrapper-class="md:col-span-8" />
                <x-form.readonly label="Com o funcionário agora"
                    :value="$emprestimo ? 'Sim — termo #' . $emprestimo->movimentacao_id : 'Não'" wrapper-class="md:col-span-4" />
            </x-form.grid>
        </x-form.fieldset>

        <x-form.fieldset legend="Problema">
            <x-form.grid>
                <x-form.readonly label="Reportado em" :value="$data($ocorrencia->reportado_em)" wrapper-class="md:col-span-4" />
                <x-form.readonly label="Data do problema" :value="$data($ocorrencia->data_problema)" wrapper-class="md:col-span-4" />
                <x-form.readonly label="Previsão" :value="$data($ocorrencia->previsao_em)" wrapper-class="md:col-span-4" />
            </x-form.grid>
            <x-form.readonly label="Erro / problema" :value="$ocorrencia->problema" />
            <x-form.grid>
                <x-form.readonly label="Canal" :value="$ocorrencia->canal" wrapper-class="md:col-span-4" />
                <x-form.readonly label="Protocolo" :value="$ocorrencia->protocolo" wrapper-class="md:col-span-4" />
                <x-form.readonly label="Valor cobrado do colaborador" :value="$ocorrencia->valor_cobrado_formatado"
                    wrapper-class="md:col-span-4" />
            </x-form.grid>
        </x-form.fieldset>

        <x-form.fieldset legend="Liberação">
            <x-form.grid>
                <x-form.readonly label="Liberado pela TI em" :value="$data($ocorrencia->liberado_em)" wrapper-class="md:col-span-4" />
                <x-form.readonly label="Solução" :value="$ocorrencia->solucao" multiline wrapper-class="md:col-span-8" />
            </x-form.grid>
        </x-form.fieldset>

        <x-form.readonly label="Observação" :value="$ocorrencia->observacao" multiline />

        <x-form.actions>
            <x-ui.button :href="route('ocorrencias.index')" icon="fa-solid fa-arrow-left">Voltar</x-ui.button>

            <div class="flex flex-wrap items-center gap-3">
                @if ($emprestimo && ! $ocorrencia->troca)
                    <x-ui.button :href="route('movimentacoes.troca.create', ['ocorrencia_id' => $ocorrencia->id])"
                        variant="soft" icon="fa-solid fa-right-left">Registrar troca</x-ui.button>
                @endif
                <x-ui.button :href="route('ocorrencias.edit', $ocorrencia)" icon="fa-solid fa-pen-to-square">Editar</x-ui.button>
                <x-ui.delete-button :action="route('ocorrencias.destroy', $ocorrencia)"
                    :confirm="'Excluir a ocorrência #' . $ocorrencia->id . '? Use só para registros feitos por engano.'" />
            </div>
        </x-form.actions>
    </x-ui.card>
@endsection
