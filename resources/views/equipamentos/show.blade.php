@extends('layouts.main_layout')

@use('App\Models\Impressora')

@section('content')
    @php
        $lista = fn ($valores) => filled($valores) ? implode(', ', (array) $valores) : null;
        $computador = $equipamento->computador;
        $monitor = $equipamento->monitor;
        $impressora = $equipamento->impressora;
        $movel = $equipamento->dispositivoMovel;
    @endphp

    <x-ui.card size="lg">
        <x-ui.card-header :title="'Equipamento — #' . $equipamento->id . ($equipamento->nome_exibicao ? ' · ' . $equipamento->nome_exibicao : '')">
            <x-slot:subtitle>
                <x-ui.timestamps :model="$equipamento" />
            </x-slot:subtitle>
        </x-ui.card-header>

        <x-form.grid>
            <x-form.readonly id="equipamento_id" label="ID" :value="$equipamento->id" wrapper-class="md:col-span-3" />
            <x-form.active-toggle id="equipamento_ativo" :checked="$equipamento->ativo" disabled
                wrapper-class="md:col-span-3" />
            <x-form.readonly id="status" label="Status" :value="$equipamento->status_rotulo"
                wrapper-class="md:col-span-6" />
        </x-form.grid>

        <x-form.fieldset legend="Identificação">
            <x-form.grid>
                <x-form.readonly id="tipo_nome" label="Tipo do Equipamento" :value="$equipamento->tipoEquipamento?->nome"
                    wrapper-class="md:col-span-6" />
                <x-form.readonly id="identificacao" label="Identificação interna" :value="$equipamento->identificacao"
                    wrapper-class="md:col-span-6" />
                <x-form.readonly id="fabricante" label="Fabricante" :value="$equipamento->fabricante"
                    wrapper-class="md:col-span-6" />
                <x-form.readonly id="modelo" label="Modelo" :value="$equipamento->modelo" wrapper-class="md:col-span-6" />
                <x-form.readonly id="numero_serie" label="Número de Série" :value="$equipamento->numero_serie"
                    wrapper-class="md:col-span-6" />
                <x-form.readonly id="patrimonio" label="Patrimônio" :value="$equipamento->patrimonio"
                    wrapper-class="md:col-span-6" />
            </x-form.grid>
        </x-form.fieldset>

        <x-form.fieldset legend="Aquisição">
            <x-form.grid>
                <x-form.readonly id="data_compra" label="Data da compra"
                    :value="$equipamento->data_compra?->format('d/m/Y')" wrapper-class="md:col-span-4" />
                <x-form.readonly id="valor_compra" label="Valor da compra"
                    :value="filled($equipamento->valor_compra) ? 'R$ ' . number_format((float) $equipamento->valor_compra, 2, ',', '.') : null"
                    wrapper-class="md:col-span-4" />
                <x-form.readonly id="nota_fiscal" label="Nº da nota fiscal" :value="$equipamento->nota_fiscal"
                    wrapper-class="md:col-span-4" />
                <x-form.readonly id="chave_acesso_nf" label="Chave de acesso da NF-e"
                    :value="$equipamento->chave_acesso_nf_formatada" wrapper-class="md:col-span-12" />
            </x-form.grid>
        </x-form.fieldset>

        @if ($computador)
            <x-form.fieldset legend="Ficha técnica — Computador">
                <x-form.grid>
                    <x-form.readonly label="Sistema operacional" :value="$computador->sistema_operacional" wrapper-class="md:col-span-6" />
                    <x-form.readonly label="Processador" :value="$computador->processador" wrapper-class="md:col-span-6" />
                    <x-form.readonly label="Placa de vídeo" :value="$computador->placa_video" wrapper-class="md:col-span-12" />
                    <x-form.readonly label="Memória"
                        :value="trim($computador->memoria_gb . ' GB ' . $computador->memoria_tipo . ' ' . $computador->memoria_formato)"
                        wrapper-class="md:col-span-6" />
                    <x-form.readonly label="Armazenamento"
                        :value="$computador->armazenamento_gb . ' GB ' . $computador->armazenamento_tipo" wrapper-class="md:col-span-6" />
                    <x-form.readonly label="MAC do cabo de rede" :value="$computador->mac_ethernet" wrapper-class="md:col-span-6" />
                    <x-form.readonly label="MAC do Wi-Fi"
                        :value="$computador->possui_wifi ? ($computador->mac_wifi ?? 'Possui Wi-Fi') : 'Não possui Wi-Fi'"
                        wrapper-class="md:col-span-6" />
                    <x-form.readonly label="Portas de vídeo" :value="$lista($computador->portas_video)" wrapper-class="md:col-span-6" />
                    <x-form.readonly label="Outras portas" :value="$computador->outras_portas" wrapper-class="md:col-span-6" />
                    <x-form.readonly label="ID do AnyDesk" :value="$computador->anydesk_id" wrapper-class="md:col-span-6" />
                </x-form.grid>
            </x-form.fieldset>
        @elseif ($monitor)
            <x-form.fieldset legend="Ficha técnica — Monitor">
                <x-form.grid>
                    <x-form.readonly label="Tamanho" :value="str_replace('.', ',', (string) $monitor->polegadas) . ' polegadas'"
                        wrapper-class="md:col-span-4" />
                    <x-form.readonly label="Tipo de tela" :value="$monitor->tipo_tela" wrapper-class="md:col-span-4" />
                    <x-form.readonly label="Portas de vídeo" :value="$lista($monitor->portas_video)" wrapper-class="md:col-span-4" />
                </x-form.grid>
            </x-form.fieldset>
        @elseif ($impressora)
            <x-form.fieldset legend="Ficha técnica — Impressora">
                <x-form.grid>
                    <x-form.readonly label="Tecnologia" :value="Impressora::TECNOLOGIAS[$impressora->tecnologia] ?? $impressora->tecnologia"
                        wrapper-class="md:col-span-4" />
                    <x-form.readonly label="Conexões"
                        :value="$lista(array_map(fn ($c) => Impressora::CONEXOES[$c] ?? $c, $impressora->conexoes ?? []))"
                        wrapper-class="md:col-span-4" />
                    <x-form.readonly label="MAC" :value="$impressora->mac" wrapper-class="md:col-span-4" />
                </x-form.grid>
            </x-form.fieldset>
        @elseif ($movel)
            <x-form.fieldset legend="Ficha técnica — Dispositivo móvel">
                <x-form.grid>
                    <x-form.readonly label="IMEI 1" :value="$movel->imei_1" wrapper-class="md:col-span-4" />
                    <x-form.readonly label="IMEI 2" :value="$movel->imei_2" wrapper-class="md:col-span-4" />
                    <x-form.readonly label="MAC" :value="$movel->mac" wrapper-class="md:col-span-4" />
                </x-form.grid>
            </x-form.fieldset>
        @endif

        <x-form.readonly id="descricao" label="Observação" :value="$equipamento->descricao" multiline />

        <x-form.actions>
            <x-ui.button :href="route('equipamentos.index')" icon="fa-solid fa-arrow-left">Voltar</x-ui.button>

            <div class="flex items-center gap-3">
                <x-ui.button :href="route('relatorios.equipamentos.historico', $equipamento)" target="_blank"
                    rel="noopener noreferrer" icon="fa-solid fa-clock-rotate-left">Histórico</x-ui.button>
                <x-ui.button :href="route('equipamentos.edit', $equipamento)" icon="fa-solid fa-pen-to-square">Editar</x-ui.button>
                <x-ui.delete-button :action="route('equipamentos.destroy', $equipamento)"
                    :confirm="'Excluir o equipamento #' . $equipamento->id . ($equipamento->patrimonio ? ' (patrimônio ' . $equipamento->patrimonio . ')' : '') . '?'" />
            </div>
        </x-form.actions>
    </x-ui.card>
@endsection
