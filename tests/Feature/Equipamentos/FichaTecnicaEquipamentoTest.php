<?php

namespace Tests\Feature\Equipamentos;

use App\Enums\CategoriaEquipamento;
use App\Models\Computador;
use App\Models\Equipamento;
use App\Models\TipoEquipamento;
use App\Rules\ChaveAcessoNfe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Versão 2.0: campos comuns do equipamento e ficha técnica por categoria do tipo.
 */
class FichaTecnicaEquipamentoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->autenticar();
    }

    private function tipo(CategoriaEquipamento $categoria): TipoEquipamento
    {
        return TipoEquipamento::factory()->categoria($categoria)->create();
    }

    private function dados(TipoEquipamento $tipo, array $sobrescrever = []): array
    {
        return array_replace_recursive([
            'tipo_equipamento_id' => $tipo->id,
            'status' => 'disponivel',
            'fabricante' => 'Dell',
            'modelo' => 'G15 5530',
            'numero_serie' => 'bh43p74',
            'valor_compra' => '1.200,00',
        ], $sobrescrever);
    }

    private function fichaComputador(array $sobrescrever = []): array
    {
        return ['computador' => array_replace([
            'sistema_operacional' => 'Windows 11 Home x64',
            'processador' => 'Intel Core i5-13450HX',
            'memoria_gb' => 16,
            'memoria_tipo' => 'DDR5',
            'memoria_formato' => 'SODIMM',
            'armazenamento_gb' => 512,
            'armazenamento_tipo' => 'SSD NVMe',
            'mac_ethernet' => 'd0-c1-b5-2f-d1-0a',
            'possui_wifi' => '1',
            'mac_wifi' => '44a3bb172e8e',
            'portas_video' => ['HDMI', 'DisplayPort'],
            'anydesk_id' => '1637134398',
        ], $sobrescrever)];
    }

    public function test_cadastra_computador_com_ficha_tecnica_e_normaliza_os_macs(): void
    {
        $tipo = $this->tipo(CategoriaEquipamento::COMPUTADOR);

        $this->post(route('equipamentos.store'), $this->dados($tipo, ['identificacao' => 'nurtic121'] + $this->fichaComputador()))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('equipamentos.index'));

        $equipamento = Equipamento::sole();
        $this->assertSame('NURTIC121', $equipamento->identificacao);
        $this->assertSame('BH43P74', $equipamento->numero_serie);
        $this->assertSame('1200.00', $equipamento->valor_compra);
        $this->assertSame('Dell G15 5530', $equipamento->nome_exibicao);

        $computador = $equipamento->computador;
        $this->assertSame('D0:C1:B5:2F:D1:0A', $computador->mac_ethernet);
        $this->assertSame('44:A3:BB:17:2E:8E', $computador->mac_wifi);
        $this->assertSame(['HDMI', 'DisplayPort'], $computador->portas_video);
        $this->assertSame(16, $computador->memoria_gb);
    }

    public function test_computador_sem_wifi_descarta_o_mac_do_wifi(): void
    {
        $tipo = $this->tipo(CategoriaEquipamento::COMPUTADOR);

        $this->post(route('equipamentos.store'), $this->dados($tipo, $this->fichaComputador([
            'possui_wifi' => '0',
            'mac_wifi' => '44:A3:BB:17:2E:8E',
        ])))->assertSessionHasNoErrors();

        $this->assertNull(Computador::sole()->mac_wifi);
    }

    public function test_computador_exige_os_campos_obrigatorios_da_ficha(): void
    {
        $tipo = $this->tipo(CategoriaEquipamento::COMPUTADOR);

        $this->post(route('equipamentos.store'), $this->dados($tipo, ['computador' => ['sistema_operacional' => '']]))
            ->assertSessionHasErrors([
                'computador.sistema_operacional', 'computador.processador', 'computador.memoria_gb',
                'computador.armazenamento_gb', 'computador.armazenamento_tipo',
            ]);

        $this->assertSame(0, Equipamento::count());
    }

    public function test_rejeita_mac_invalido_e_mac_repetido(): void
    {
        $tipo = $this->tipo(CategoriaEquipamento::COMPUTADOR);

        $this->post(route('equipamentos.store'), $this->dados($tipo, $this->fichaComputador(['mac_ethernet' => '12:34'])))
            ->assertSessionHasErrors(['computador.mac_ethernet' => 'Informe o MAC no formato AA:BB:CC:DD:EE:FF.']);

        $this->post(route('equipamentos.store'), $this->dados($tipo, $this->fichaComputador()))->assertSessionHasNoErrors();

        $this->post(route('equipamentos.store'), $this->dados($tipo, [
            'numero_serie' => 'OUTRO-SN',
        ] + $this->fichaComputador(['mac_wifi' => 'AA:AA:AA:AA:AA:AA'])))
            ->assertSessionHasErrors(['computador.mac_ethernet' => 'Este MAC já está cadastrado em outro equipamento.']);
    }

    public function test_cadastra_monitor_aceitando_polegadas_com_virgula(): void
    {
        $tipo = $this->tipo(CategoriaEquipamento::MONITOR);

        $this->post(route('equipamentos.store'), $this->dados($tipo, [
            'monitor' => ['polegadas' => '21,5', 'tipo_tela' => 'LED', 'portas_video' => ['HDMI', 'VGA']],
        ]))->assertSessionHasNoErrors();

        $monitor = Equipamento::sole()->monitor;
        $this->assertSame('21.5', $monitor->polegadas);
        $this->assertSame(['HDMI', 'VGA'], $monitor->portas_video);
    }

    public function test_impressora_exige_ao_menos_uma_conexao(): void
    {
        $tipo = $this->tipo(CategoriaEquipamento::IMPRESSORA);

        $this->post(route('equipamentos.store'), $this->dados($tipo, ['impressora' => ['tecnologia' => 'tanque_tinta']]))
            ->assertSessionHasErrors(['impressora.conexoes' => 'Selecione ao menos uma conexão.']);

        $this->post(route('equipamentos.store'), $this->dados($tipo, [
            'impressora' => ['tecnologia' => 'tanque_tinta', 'conexoes' => ['usb', 'wifi'], 'mac' => 'e0bb9ef00439'],
        ]))->assertSessionHasNoErrors();

        $impressora = Equipamento::sole()->impressora;
        $this->assertSame(['usb', 'wifi'], $impressora->conexoes);
        $this->assertSame('E0:BB:9E:F0:04:39', $impressora->mac);
    }

    public function test_dispositivo_movel_valida_e_guarda_os_imeis(): void
    {
        $tipo = $this->tipo(CategoriaEquipamento::DISPOSITIVO_MOVEL);

        $this->post(route('equipamentos.store'), $this->dados($tipo, ['dispositivo_movel' => ['imei_1' => '12345']]))
            ->assertSessionHasErrors(['dispositivo_movel.imei_1' => 'O IMEI deve ter 15 dígitos.']);

        $this->post(route('equipamentos.store'), $this->dados($tipo, [
            'identificacao' => 'CELUR01',
            'dispositivo_movel' => ['imei_1' => '354 494 165 730 091', 'imei_2' => '357546515730092', 'mac' => '1C:1A:1B:CC:52:4B'],
        ]))->assertSessionHasNoErrors();

        $movel = Equipamento::sole()->dispositivoMovel;
        $this->assertSame('354494165730091', $movel->imei_1);
        $this->assertSame('357546515730092', $movel->imei_2);

        $this->post(route('equipamentos.store'), $this->dados($tipo, [
            'numero_serie' => 'OUTRO',
            'dispositivo_movel' => ['imei_1' => '354494165730091'],
        ]))->assertSessionHasErrors(['dispositivo_movel.imei_1' => 'Este IMEI já está cadastrado em outro equipamento.']);
    }

    public function test_tipo_generico_nao_tem_ficha_e_ignora_dados_de_outras_categorias(): void
    {
        $tipo = $this->tipo(CategoriaEquipamento::GENERICO);

        $this->post(route('equipamentos.store'), $this->dados($tipo, $this->fichaComputador(['processador' => ''])))
            ->assertSessionHasNoErrors();

        $this->assertNull(Equipamento::sole()->computador);
        $this->assertSame(0, Computador::count());
    }

    public function test_patrimonio_obrigatorio_acima_de_1500_e_opcional_abaixo(): void
    {
        $tipo = $this->tipo(CategoriaEquipamento::GENERICO);

        $this->post(route('equipamentos.store'), $this->dados($tipo, ['valor_compra' => '1.500,01']))
            ->assertSessionHasErrors(['patrimonio' => 'Informe o patrimônio: obrigatório para equipamentos acima de R$ 1.500,00.']);

        $this->post(route('equipamentos.store'), $this->dados($tipo, ['valor_compra' => '1.500,00']))
            ->assertSessionHasNoErrors();

        $this->post(route('equipamentos.store'), $this->dados($tipo, [
            'numero_serie' => 'SN-2', 'valor_compra' => '2.300,00', 'patrimonio' => '004512',
        ]))->assertSessionHasNoErrors();

        $this->assertSame(2, Equipamento::count());
    }

    /** Chave de acesso válida (DV calculado) com o número da nota nas posições 26 a 34. */
    private function chaveNfe(string $numeroNota): string
    {
        $base = '4124091234567800019955001' . str_pad($numeroNota, 9, '0', STR_PAD_LEFT) . '112345678';

        return $base . ChaveAcessoNfe::digitoVerificador($base);
    }

    public function test_valor_da_compra_aceita_apenas_numeros(): void
    {
        $tipo = $this->tipo(CategoriaEquipamento::GENERICO);

        $this->post(route('equipamentos.store'), $this->dados($tipo, ['valor_compra' => 'mil reais']))
            ->assertSessionHasErrors(['valor_compra' => 'Informe o valor apenas com números (ex.: 1.234,56).']);

        $this->post(route('equipamentos.store'), $this->dados($tipo, ['valor_compra' => '10,555']))
            ->assertSessionHasErrors(['valor_compra' => 'O valor pode ter no máximo 2 casas decimais.']);

        $this->post(route('equipamentos.store'), $this->dados($tipo, ['valor_compra' => '1.450']))
            ->assertSessionHasNoErrors();

        $this->assertSame('1450.00', Equipamento::sole()->valor_compra);
    }

    public function test_nota_fiscal_aceita_apenas_numeros(): void
    {
        $tipo = $this->tipo(CategoriaEquipamento::GENERICO);

        $this->post(route('equipamentos.store'), $this->dados($tipo, ['nota_fiscal' => 'NF-12A']))
            ->assertSessionHasErrors(['nota_fiscal' => 'Informe apenas o número da nota fiscal (até 9 dígitos).']);

        $this->post(route('equipamentos.store'), $this->dados($tipo, ['nota_fiscal' => '000.012.345']))
            ->assertSessionHasNoErrors();

        $this->assertSame('12345', Equipamento::sole()->nota_fiscal);
    }

    public function test_chave_de_acesso_valida_digito_e_preenche_o_numero_da_nota(): void
    {
        $tipo = $this->tipo(CategoriaEquipamento::GENERICO);
        $chave = $this->chaveNfe('12345');
        $chaveInvalida = substr($chave, 0, 43) . (((int) $chave[43] + 1) % 10);

        $this->post(route('equipamentos.store'), $this->dados($tipo, ['chave_acesso_nf' => $chaveInvalida]))
            ->assertSessionHasErrors(['chave_acesso_nf' => 'Chave de acesso inválida: confira os números digitados.']);

        $this->post(route('equipamentos.store'), $this->dados($tipo, ['chave_acesso_nf' => '1234']))
            ->assertSessionHasErrors(['chave_acesso_nf' => 'A chave de acesso deve ter 44 dígitos.']);

        $this->post(route('equipamentos.store'), $this->dados($tipo, ['nota_fiscal' => '999', 'chave_acesso_nf' => $chave]))
            ->assertSessionHasErrors(['chave_acesso_nf' => 'A chave de acesso não corresponde à nota fiscal nº 999.']);

        // Só a chave, em blocos de 4 como no DANFE: o número da nota vem dela.
        $this->post(route('equipamentos.store'), $this->dados($tipo, ['chave_acesso_nf' => implode(' ', str_split($chave, 4))]))
            ->assertSessionHasNoErrors();

        $equipamento = Equipamento::sole();
        $this->assertSame($chave, $equipamento->chave_acesso_nf);
        $this->assertSame('12345', $equipamento->nota_fiscal);
    }

    public function test_exige_fabricante_e_modelo(): void
    {
        $tipo = $this->tipo(CategoriaEquipamento::GENERICO);

        $this->post(route('equipamentos.store'), $this->dados($tipo, ['fabricante' => '', 'modelo' => '']))
            ->assertSessionHasErrors(['fabricante', 'modelo']);
    }

    public function test_trocar_o_tipo_para_outra_categoria_remove_a_ficha_anterior(): void
    {
        $tipoComputador = $this->tipo(CategoriaEquipamento::COMPUTADOR);
        $tipoMonitor = $this->tipo(CategoriaEquipamento::MONITOR);

        $this->post(route('equipamentos.store'), $this->dados($tipoComputador, $this->fichaComputador()));
        $equipamento = Equipamento::sole();

        $this->put(route('equipamentos.update', $equipamento), $this->dados($tipoMonitor, [
            'monitor' => ['polegadas' => '24'],
        ]))->assertSessionHasNoErrors();

        $equipamento->refresh();
        $this->assertNull($equipamento->computador);
        $this->assertSame('24.0', $equipamento->monitor->polegadas);
    }

    public function test_edicao_atualiza_a_ficha_existente(): void
    {
        $tipo = $this->tipo(CategoriaEquipamento::COMPUTADOR);
        $this->post(route('equipamentos.store'), $this->dados($tipo, $this->fichaComputador()));
        $equipamento = Equipamento::sole();

        $this->put(route('equipamentos.update', $equipamento), $this->dados($tipo, $this->fichaComputador(['memoria_gb' => 32])))
            ->assertSessionHasNoErrors();

        $this->assertSame(1, Computador::count());
        $this->assertSame(32, $equipamento->refresh()->computador->memoria_gb);
    }

    public function test_telas_exibem_os_campos_da_ficha(): void
    {
        $tipo = $this->tipo(CategoriaEquipamento::COMPUTADOR);
        $this->post(route('equipamentos.store'), $this->dados($tipo, $this->fichaComputador()));
        $equipamento = Equipamento::sole();

        $this->get(route('equipamentos.create'))->assertOk()
            ->assertSee('data-ficha="computador"', false)
            ->assertSee('data-ficha="dispositivo_movel"', false);

        $this->get(route('equipamentos.edit', $equipamento))->assertOk()
            ->assertSee('Intel Core i5-13450HX')
            ->assertSee('D0:C1:B5:2F:D1:0A');

        $this->get(route('equipamentos.show', $equipamento))->assertOk()
            ->assertSee('Ficha técnica — Computador')
            ->assertSee('16 GB DDR5 SODIMM')
            ->assertSee('HDMI, DisplayPort');
    }

    public function test_busca_por_mac_e_imei_na_listagem(): void
    {
        $tipoComputador = $this->tipo(CategoriaEquipamento::COMPUTADOR);
        $tipoMovel = $this->tipo(CategoriaEquipamento::DISPOSITIVO_MOVEL);

        $this->post(route('equipamentos.store'), $this->dados($tipoComputador, ['modelo' => 'PC-ALVO'] + $this->fichaComputador()));
        $this->post(route('equipamentos.store'), $this->dados($tipoMovel, [
            'numero_serie' => 'SN-CEL', 'modelo' => 'CEL-ALVO', 'dispositivo_movel' => ['imei_1' => '354494165730091'],
        ]));

        $this->get(route('equipamentos.index', ['campo' => 'imei_mac', 'busca' => 'd0c1b5']))
            ->assertOk()->assertSee('PC-ALVO')->assertDontSee('CEL-ALVO');

        $this->get(route('equipamentos.index', ['busca' => '3544941657']))
            ->assertOk()->assertSee('CEL-ALVO')->assertDontSee('PC-ALVO');
    }

    public function test_busca_por_texto_nao_confunde_letras_com_mac(): void
    {
        $tipoComputador = $this->tipo(CategoriaEquipamento::COMPUTADOR);
        $tipoMovel = $this->tipo(CategoriaEquipamento::DISPOSITIVO_MOVEL);

        // MAC com "DE", que também são letras de "Dell".
        $this->post(route('equipamentos.store'), $this->dados($tipoComputador, [
            'fabricante' => 'Lenovo', 'modelo' => 'PC-LENOVO',
        ] + $this->fichaComputador(['mac_ethernet' => 'DE:11:22:33:44:55', 'possui_wifi' => '0'])))->assertSessionHasNoErrors();
        $this->post(route('equipamentos.store'), $this->dados($tipoMovel, [
            'numero_serie' => 'SN-DELL', 'fabricante' => 'Dell', 'modelo' => 'CEL-DELL',
        ]))->assertSessionHasNoErrors();

        $this->get(route('equipamentos.index', ['busca' => 'Dell']))
            ->assertOk()->assertSee('CEL-DELL')->assertDontSee('PC-LENOVO');

        $this->get(route('equipamentos.index', ['busca' => 'de:11:22']))
            ->assertOk()->assertSee('PC-LENOVO')->assertDontSee('CEL-DELL');
    }

    public function test_tipo_de_equipamento_e_cadastrado_com_categoria(): void
    {
        $this->post(route('tipo-equipamentos.store'), ['nome' => '  Tablet   Android ', 'categoria' => 'dispositivo_movel'])
            ->assertSessionHasNoErrors();

        $tipo = TipoEquipamento::where('nome', 'Tablet Android')->sole();
        $this->assertSame(CategoriaEquipamento::DISPOSITIVO_MOVEL, $tipo->categoria);

        $this->post(route('tipo-equipamentos.store'), ['nome' => 'Outro', 'categoria' => 'nave'])
            ->assertSessionHasErrors('categoria');
    }

    public function test_categoria_do_tipo_fica_travada_quando_ha_equipamentos(): void
    {
        $tipo = $this->tipo(CategoriaEquipamento::MONITOR);
        $dados = ['nome' => $tipo->nome, 'categoria' => 'computador', 'ativo' => 1];

        Equipamento::factory()->create(['tipo_equipamento_id' => $tipo->id]);

        $this->put(route('tipo-equipamentos.update', $tipo), $dados)
            ->assertSessionHasErrors(['categoria' => 'A categoria não pode ser alterada porque já existem equipamentos cadastrados com este tipo.']);

        $this->assertSame(CategoriaEquipamento::MONITOR, $tipo->refresh()->categoria);
    }
}
