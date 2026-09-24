{{--
    Campos do cadastro/edição de equipamento. Variáveis: $equipamento (null no cadastro), $tipos (id, nome, categoria),
    $listaStatus (cadastro) ou $emprestimoEmAberto (edição).
    A ficha técnica exibida depende da categoria do tipo (resources/js/equipamentos/equipamento-form.js):
    as fichas das outras categorias ficam ocultas e desabilitadas, então não são enviadas.
--}}
@use('App\Enums\CategoriaEquipamento')
@use('App\Models\Computador')
@use('App\Models\Equipamento')
@use('App\Models\Impressora')
@use('App\Models\Monitor')

@php
    $tipoSelecionado = old('tipo_equipamento_id', $equipamento?->tipo_equipamento_id);
    $categoriasPorTipo = $tipos->mapWithKeys(fn ($tipo) => [$tipo->id => $tipo->categoria->value]);
    $categoriaAtual = $categoriasPorTipo[$tipoSelecionado] ?? null;

    $computador = $equipamento?->computador;
    $monitor = $equipamento?->monitor;
    $impressora = $equipamento?->impressora;
    $movel = $equipamento?->dispositivoMovel;

    $lista = fn (array $valores) => array_combine($valores, $valores);
    $fichaVisivel = fn (CategoriaEquipamento $categoria) => $categoriaAtual === $categoria->value;
    $valorFormatado = filled($equipamento?->valor_compra) ? number_format((float) $equipamento->valor_compra, 2, ',', '.') : null;
@endphp

<x-form.fieldset legend="Identificação">
    <x-form.grid>
        <x-form.select name="tipo_equipamento_id" label="Tipo de Equipamento" required placeholder="Selecione..."
            :options="$tipos" option-label="nome" :value="$tipoSelecionado" wrapper-class="md:col-span-6"
            data-categorias="{{ $categoriasPorTipo->toJson() }}" />
        @include($equipamento ? 'equipamentos.partials.status-edicao' : 'equipamentos.partials.status-cadastro')

        <x-form.input name="identificacao" label="Identificação interna" maxlength="50" :value="$equipamento?->identificacao"
            placeholder="Ex.: NURTIC121, CELUR01, PP1EMBBHE001" help="Nome na rede ou código interno. Não pode repetir."
            wrapper-class="md:col-span-12" />

        <x-form.input name="fabricante" label="Fabricante" required maxlength="60" :value="$equipamento?->fabricante"
            placeholder="Ex.: Dell, Samsung, SUNMI" wrapper-class="md:col-span-6" />
        <x-form.input name="modelo" label="Modelo" required maxlength="80" :value="$equipamento?->modelo"
            placeholder="Ex.: G15 5530, Galaxy A05s, T6900 (P2)" wrapper-class="md:col-span-6" />

        <x-form.input name="numero_serie" label="Número de Série" maxlength="100" :value="$equipamento?->numero_serie"
            wrapper-class="md:col-span-6" />
        <x-form.input name="patrimonio" label="Patrimônio" maxlength="50" :value="$equipamento?->patrimonio"
            :help="'Número da plaquinha. Obrigatório acima de R$ ' . number_format(Equipamento::VALOR_MINIMO_PATRIMONIO, 2, ',', '.') . '.'"
            wrapper-class="md:col-span-6" />
    </x-form.grid>
</x-form.fieldset>

<x-form.fieldset legend="Aquisição">
    <x-form.grid>
        <x-form.input type="date" name="data_compra" label="Data da compra" :max="today()->toDateString()"
            :value="$equipamento?->data_compra?->format('Y-m-d')" wrapper-class="md:col-span-4" />
        {{-- Máscara de moeda (equipamento-form.js); o EquipamentoRequest normaliza "1.234,56" para 1234.56 --}}
        <x-form.input name="valor_compra" label="Valor da compra (R$)" inputmode="numeric" placeholder="0,00"
            :value="$valorFormatado" data-mascara="moeda" wrapper-class="md:col-span-4" />
        <x-form.input name="nota_fiscal" label="Nº da nota fiscal" inputmode="numeric" maxlength="9"
            :value="$equipamento?->nota_fiscal" data-mascara="digitos" help="Somente números."
            wrapper-class="md:col-span-4" />
        <x-form.input name="chave_acesso_nf" label="Chave de acesso da NF-e" inputmode="numeric" maxlength="54"
            :value="$equipamento?->chave_acesso_nf_formatada" data-mascara="chave-nfe"
            placeholder="0000 0000 0000 0000 0000 0000 0000 0000 0000 0000 0000"
            help="Opcional. 44 dígitos; se informada, o número da nota é preenchido a partir dela."
            wrapper-class="md:col-span-12" />
    </x-form.grid>
</x-form.fieldset>

{{-- Fichas técnicas por categoria --}}
<x-form.fieldset legend="Ficha técnica — Computador" data-ficha="computador" :hidden="!$fichaVisivel(CategoriaEquipamento::COMPUTADOR)"
    :disabled="!$fichaVisivel(CategoriaEquipamento::COMPUTADOR)">
    <x-form.grid>
        <x-form.input name="computador[sistema_operacional]" label="Sistema operacional" required maxlength="60"
            :value="$computador?->sistema_operacional" placeholder="Ex.: Windows 11 Pro x64" wrapper-class="md:col-span-6" />
        <x-form.input name="computador[processador]" label="Processador" required maxlength="100"
            :value="$computador?->processador" placeholder="Ex.: Intel Core i5-13450HX" wrapper-class="md:col-span-6" />

        <x-form.input name="computador[placa_video]" label="Placa de vídeo" maxlength="100"
            :value="$computador?->placa_video" placeholder="Ex.: NVIDIA RTX 4050 6GB" wrapper-class="md:col-span-12" />

        <x-form.input type="number" name="computador[memoria_gb]" label="Memória (GB)" required min="1" max="4096"
            :value="$computador?->memoria_gb" wrapper-class="md:col-span-4" />
        <x-form.select name="computador[memoria_tipo]" label="Tipo de memória" placeholder="—"
            :options="$lista(Computador::TIPOS_MEMORIA)" :value="$computador?->memoria_tipo" wrapper-class="md:col-span-4" />
        <x-form.select name="computador[memoria_formato]" label="Formato da memória" placeholder="—"
            :options="$lista(Computador::FORMATOS_MEMORIA)" :value="$computador?->memoria_formato" wrapper-class="md:col-span-4" />

        <x-form.input type="number" name="computador[armazenamento_gb]" label="Armazenamento (GB)" required min="1"
            :value="$computador?->armazenamento_gb" wrapper-class="md:col-span-6" />
        <x-form.select name="computador[armazenamento_tipo]" label="Tipo de armazenamento" required placeholder="Selecione..."
            :options="$lista(Computador::TIPOS_ARMAZENAMENTO)" :value="$computador?->armazenamento_tipo" wrapper-class="md:col-span-6" />

        <x-form.input name="computador[mac_ethernet]" label="MAC do cabo de rede" maxlength="17"
            :value="$computador?->mac_ethernet" placeholder="AA:BB:CC:DD:EE:FF" wrapper-class="md:col-span-5" />
        <div class="flex items-end pb-2 md:col-span-2">
            <x-form.checkbox name="computador[possui_wifi]" label="Possui Wi-Fi" :checked="$computador?->possui_wifi"
                data-possui-wifi />
        </div>
        <x-form.input name="computador[mac_wifi]" label="MAC do Wi-Fi" maxlength="17" :value="$computador?->mac_wifi"
            placeholder="AA:BB:CC:DD:EE:FF" wrapper-class="md:col-span-5" data-mac-wifi />

        <x-form.checkbox-group name="computador[portas_video]" label="Portas de vídeo"
            :options="$lista(Computador::PORTAS_VIDEO)" :value="$computador?->portas_video" wrapper-class="md:col-span-12" />

        <x-form.input name="computador[outras_portas]" label="Outras portas" maxlength="150"
            :value="$computador?->outras_portas" placeholder="Ex.: 3x USB, 1x USB-C, 1x RJ45, áudio P2"
            wrapper-class="md:col-span-8" />
        <x-form.input name="computador[anydesk_id]" label="ID do AnyDesk" maxlength="20" :value="$computador?->anydesk_id"
            help="Somente o ID. Não informe a senha." wrapper-class="md:col-span-4" />
    </x-form.grid>
</x-form.fieldset>

<x-form.fieldset legend="Ficha técnica — Monitor" data-ficha="monitor" :hidden="!$fichaVisivel(CategoriaEquipamento::MONITOR)"
    :disabled="!$fichaVisivel(CategoriaEquipamento::MONITOR)">
    <x-form.grid>
        <x-form.input name="monitor[polegadas]" label="Tamanho (polegadas)" required inputmode="decimal"
            :value="$monitor ? str_replace('.', ',', (string) $monitor->polegadas) : null" placeholder="Ex.: 21,5"
            wrapper-class="md:col-span-4" />
        <x-form.select name="monitor[tipo_tela]" label="Tipo de tela" placeholder="—"
            :options="$lista(Monitor::TIPOS_TELA)" :value="$monitor?->tipo_tela" wrapper-class="md:col-span-4" />
        <x-form.checkbox-group name="monitor[portas_video]" label="Portas de vídeo"
            :options="$lista(Computador::PORTAS_VIDEO)" :value="$monitor?->portas_video" wrapper-class="md:col-span-12" />
    </x-form.grid>
</x-form.fieldset>

<x-form.fieldset legend="Ficha técnica — Impressora" data-ficha="impressora" :hidden="!$fichaVisivel(CategoriaEquipamento::IMPRESSORA)"
    :disabled="!$fichaVisivel(CategoriaEquipamento::IMPRESSORA)">
    <x-form.grid>
        <x-form.select name="impressora[tecnologia]" label="Tecnologia" required placeholder="Selecione..."
            :options="Impressora::TECNOLOGIAS" :value="$impressora?->tecnologia" wrapper-class="md:col-span-6" />
        <x-form.input name="impressora[mac]" label="MAC" maxlength="17" :value="$impressora?->mac"
            placeholder="AA:BB:CC:DD:EE:FF" help="Para impressoras em rede ou Wi-Fi." wrapper-class="md:col-span-6" />
        <x-form.checkbox-group name="impressora[conexoes]" label="Conexões" required :options="Impressora::CONEXOES"
            :value="$impressora?->conexoes" wrapper-class="md:col-span-12" />
    </x-form.grid>
</x-form.fieldset>

<x-form.fieldset legend="Ficha técnica — Dispositivo móvel" data-ficha="dispositivo_movel"
    :hidden="!$fichaVisivel(CategoriaEquipamento::DISPOSITIVO_MOVEL)" :disabled="!$fichaVisivel(CategoriaEquipamento::DISPOSITIVO_MOVEL)">
    <x-form.grid>
        <x-form.input name="dispositivo_movel[imei_1]" label="IMEI 1" inputmode="numeric" maxlength="15" data-mascara="digitos"
            :value="$movel?->imei_1" help="15 dígitos." wrapper-class="md:col-span-4" />
        <x-form.input name="dispositivo_movel[imei_2]" label="IMEI 2" inputmode="numeric" maxlength="15" data-mascara="digitos"
            :value="$movel?->imei_2" help="Aparelhos com dois chips." wrapper-class="md:col-span-4" />
        <x-form.input name="dispositivo_movel[mac]" label="MAC" maxlength="17" :value="$movel?->mac"
            placeholder="AA:BB:CC:DD:EE:FF" wrapper-class="md:col-span-4" />
    </x-form.grid>
</x-form.fieldset>

<x-form.textarea name="descricao" label="Observação" rows="3" :value="$equipamento?->descricao"
    placeholder="Informações adicionais (opcional)" />

@push('scripts')
    @vite('resources/js/equipamentos/equipamento-form.js')
@endpush
