@extends('layouts.main_layout')

@use('App\Models\Movimentacao')
@use('App\Models\MovimentacaoEquipamento')

@section('content')
    {{-- Motivo só na troca interna (na troca pelo fornecedor o antigo recebe baixa): troca-form.js --}}
    <x-form.card id="trocaForm" :action="route('movimentacoes.troca.store')" size="lg" title="Termo de Troca"
        subtitle="Substitui um equipamento em uso por outro disponível. O funcionário é o que está com o equipamento substituído.">

        @if ($ocorrencia)
            <input type="hidden" name="ocorrencia_id" value="{{ $ocorrencia->id }}">
            <x-ui.alert icon="fa-solid fa-triangle-exclamation" :title="'Troca da ocorrência #' . $ocorrencia->id">
                {{ $ocorrencia->problema }}. A ocorrência fica vinculada a esta troca.
            </x-ui.alert>
        @endif

        @if ($equipamentosEmUso->isEmpty())
            <x-ui.alert variant="warning" icon="fa-solid fa-circle-exclamation" title="Nenhum equipamento em uso">
                A troca substitui um equipamento que está com um funcionário. Registre antes um termo de responsabilidade.
            </x-ui.alert>
        @endif

        <x-form.fieldset legend="Equipamentos">
            <x-form.select name="equipamento_antigo_id" label="Equipamento substituído (em uso)" required
                placeholder="Digite o modelo, a identificação ou o funcionário…" :options="$equipamentosEmUso"
                :value="$equipamentoAntigoId" data-combobox
                help="Equipamentos em uso, com o funcionário responsável." />

            <x-form.select name="equipamento_novo_id" label="Equipamento substituto (disponível)" required
                placeholder="Digite o modelo, a identificação ou o nº de série…" :options="$equipamentosDisponiveis" data-combobox
                help="Cadastre o equipamento novo antes, se ele ainda não estiver no sistema (ex.: o que veio do fornecedor)." />

            <x-form.checkbox name="transferir_identificacao" label="Transferir a identificação interna para o substituto"
                :checked="true" />
            <p class="-mt-2 pl-6 text-xs text-ink-muted">
                Ex.: o novo celular passa a ser o CELUR01. O antigo fica sem identificação; ela continua no histórico dele.
            </p>
        </x-form.fieldset>

        <x-form.fieldset legend="Troca">
            <x-form.grid>
                <x-form.select name="tipo_troca" label="Tipo de troca" required placeholder="Selecione…"
                    :options="Movimentacao::TIPOS_TROCA" wrapper-class="md:col-span-6"
                    help="Pelo fornecedor: o substituído sai do parque (Baixado)." />

                <x-form.select name="motivo" label="Condição do substituído" placeholder="Selecione…"
                    :options="collect(MovimentacaoEquipamento::MOTIVOS_SELECIONAVEIS)->mapWithKeys(fn ($motivo) => [$motivo => MovimentacaoEquipamento::MOTIVOS_DEVOLUCAO[$motivo]])"
                    wrapper-class="md:col-span-6" data-motivo-troca
                    help="Define o status dele: manutenção, defeituoso ou disponível." />
            </x-form.grid>

            <x-form.textarea name="observacao" label="Observação (opcional)" rows="3"
                placeholder="Ex.: SecStatus 0x2014, troca aprovada pelo suporte..." />
        </x-form.fieldset>

        <x-form.actions class="pt-2">
            <x-ui.button :href="route('movimentacoes.index')" icon="fa-solid fa-arrow-left" class="text-sm">Cancelar</x-ui.button>
            <x-ui.button variant="primary" icon="fa-solid fa-right-left" class="text-sm">Registrar troca</x-ui.button>
        </x-form.actions>
    </x-form.card>
@endsection

@push('scripts')
    @vite('resources/js/movimentacoes/troca-form.js')
@endpush
