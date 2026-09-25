{{-- Ficha técnica completa do equipamento (relatório de histórico). Variável: $equipamento com as fichas carregadas. --}}
@use('App\Models\Impressora')

@php
    $lista = fn ($valores) => filled($valores) ? implode(', ', (array) $valores) : null;
    $computador = $equipamento->computador;
    $monitor = $equipamento->monitor;
    $impressora = $equipamento->impressora;
    $movel = $equipamento->dispositivoMovel;

    $linhas = match (true) {
        (bool) $computador => [
            'Sistema operacional' => $computador->sistema_operacional,
            'Processador' => $computador->processador,
            'Placa de vídeo' => $computador->placa_video,
            'Memória' => trim($computador->memoria_gb . ' GB ' . $computador->memoria_tipo . ' ' . $computador->memoria_formato),
            'Armazenamento' => $computador->armazenamento_gb . ' GB ' . $computador->armazenamento_tipo,
            'MAC do cabo de rede' => $computador->mac_ethernet,
            'Wi-Fi' => $computador->possui_wifi ? 'Sim' . ($computador->mac_wifi ? ' — MAC ' . $computador->mac_wifi : '') : 'Não',
            'Portas de vídeo' => $lista($computador->portas_video),
            'Outras portas' => $computador->outras_portas,
            'ID do AnyDesk' => $computador->anydesk_id,
        ],
        (bool) $monitor => [
            'Tamanho' => str_replace('.', ',', (string) $monitor->polegadas) . ' polegadas',
            'Tipo de tela' => $monitor->tipo_tela,
            'Portas de vídeo' => $lista($monitor->portas_video),
        ],
        (bool) $impressora => [
            'Tecnologia' => Impressora::TECNOLOGIAS[$impressora->tecnologia] ?? $impressora->tecnologia,
            'Conexões' => $lista(array_map(fn ($c) => Impressora::CONEXOES[$c] ?? $c, $impressora->conexoes ?? [])),
            'MAC' => $impressora->mac,
        ],
        (bool) $movel => [
            'IMEI 1' => $movel->imei_1,
            'IMEI 2' => $movel->imei_2,
            'MAC' => $movel->mac,
        ],
        default => [],
    };

    $linhas = array_filter($linhas, fn ($valor) => filled($valor));
@endphp

@if ($linhas)
    <div class="secao-texto">
        <h2 class="subtitulo-secao">Ficha técnica</h2>

        <table class="tabela-equipamentos tabela-ficha">
            <tbody>
                @foreach ($linhas as $rotulo => $valor)
                    <tr>
                        <td class="texto-esquerda rotulo-ficha">{{ $rotulo }}</td>
                        <td class="texto-esquerda">{{ $valor }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
