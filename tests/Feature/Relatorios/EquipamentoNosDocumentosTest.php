<?php

namespace Tests\Feature\Relatorios;

use App\Enums\CategoriaEquipamento;
use App\Models\Equipamento;
use App\Models\Movimentacao;
use App\Models\MovimentacaoEquipamento;
use App\Models\TipoEquipamento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Termos e relatórios PDF identificam o equipamento pelo tipo, fabricante/modelo, identificação e resumo técnico;
 * o relatório de histórico traz a ficha técnica completa.
 */
class EquipamentoNosDocumentosTest extends TestCase
{
    use RefreshDatabase;

    private Equipamento $notebook;

    private Equipamento $celular;

    protected function setUp(): void
    {
        parent::setUp();

        $this->autenticar();

        $this->notebook = Equipamento::factory()->emUso()->create([
            'tipo_equipamento_id' => TipoEquipamento::factory()->categoria(CategoriaEquipamento::COMPUTADOR)->create(['nome' => 'Notebook']),
            'fabricante' => 'Dell',
            'modelo' => 'G15 5530',
            'identificacao' => 'NURTIC121',
            'valor_compra' => 7899.90,
            'nota_fiscal' => '12345',
        ]);
        $this->notebook->computador()->create([
            'sistema_operacional' => 'Windows 11 Pro',
            'processador' => 'Intel Core i5-13450HX',
            'memoria_gb' => 16,
            'memoria_tipo' => 'DDR5',
            'armazenamento_gb' => 1000,
            'armazenamento_tipo' => 'SSD NVMe',
            'possui_wifi' => true,
            'mac_wifi' => '44:A3:BB:17:2E:8E',
        ]);

        $this->celular = Equipamento::factory()->emUso()->create([
            'tipo_equipamento_id' => TipoEquipamento::factory()->categoria(CategoriaEquipamento::DISPOSITIVO_MOVEL)->create(['nome' => 'Celular']),
            'fabricante' => 'Samsung',
            'modelo' => 'Galaxy A05s',
            'identificacao' => 'CELUR01',
        ]);
        $this->celular->dispositivoMovel()->create(['imei_1' => '354494165730091']);
    }

    public function test_resumo_tecnico_por_categoria(): void
    {
        $this->assertSame('Intel Core i5-13450HX · 16 GB DDR5 · 1 TB SSD NVMe', $this->notebook->resumo_tecnico);
        $this->assertSame('IMEI 354494165730091', $this->celular->resumo_tecnico);
        $this->assertNull(Equipamento::factory()->create()->resumo_tecnico);
    }

    public function test_termo_de_responsabilidade_identifica_os_equipamentos(): void
    {
        $termo = Movimentacao::factory()->create();
        $termo->equipamentos()->attach([$this->notebook->id, $this->celular->id]);

        $this->get(route('movimentacoes.termo-responsabilidade', $termo))
            ->assertOk()->assertHeader('Content-Type', 'application/pdf');

        $termo->load(['setor.empresa', 'funcionario', 'equipamentos' => fn ($q) => $q->with(['tipoEquipamento', ...Equipamento::RELACOES_FICHA])]);
        $html = view('relatorios.movimentacoes.termo-responsabilidade', ['movimentacao' => $termo])->render();

        $this->assertStringContainsString('Notebook — Dell G15 5530', $html);
        $this->assertStringContainsString('Identificação: NURTIC121', $html);
        $this->assertStringContainsString('Intel Core i5-13450HX · 16 GB DDR5 · 1 TB SSD NVMe', $html);
        $this->assertStringContainsString('Celular — Samsung Galaxy A05s', $html);
        $this->assertStringContainsString('IMEI 354494165730091', $html);
        // A ficha completa não vai para o termo.
        $this->assertStringNotContainsString('44:A3:BB:17:2E:8E', $html);
    }

    public function test_termo_de_devolucao_identifica_os_equipamentos(): void
    {
        $devolucao = Movimentacao::factory()->devolucao()->create();
        $devolucao->equipamentos()->attach($this->celular->id, ['motivo_devolucao' => 'defeito', 'devolvido_em' => today()]);

        $this->get(route('movimentacoes.termo-devolucao', $devolucao))->assertOk();

        $devolucao->load(['setor.empresa', 'funcionario', 'equipamentos' => fn ($q) => $q->with(['tipoEquipamento', ...Equipamento::RELACOES_FICHA])]);
        $html = view('relatorios.movimentacoes.termo-devolucao', ['movimentacao' => $devolucao])->render();

        $this->assertStringContainsString('Celular — Samsung Galaxy A05s', $html);
        $this->assertStringContainsString('IMEI 354494165730091', $html);
    }

    public function test_relatorio_do_funcionario_identifica_os_equipamentos_em_uso(): void
    {
        $termo = Movimentacao::factory()->create();
        MovimentacaoEquipamento::factory()->create(['movimentacao_id' => $termo->id, 'equipamento_id' => $this->notebook->id]);

        $this->get(route('relatorios.funcionarios.equipamentos', $termo->funcionario))
            ->assertOk()->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_historico_traz_aquisicao_e_ficha_tecnica_completa(): void
    {
        $this->get(route('relatorios.equipamentos.historico', $this->notebook))
            ->assertOk()->assertHeader('Content-Type', 'application/pdf');

        $this->notebook->load(['tipoEquipamento', ...Equipamento::RELACOES_FICHA]);
        $html = view('relatorios.equipamentos.historico', [
            'equipamento' => $this->notebook,
            'listaMovimentacoesResponsabilidade' => collect(),
            'linhaDoTempo' => collect(),
            'dataHoraEmissao' => now(),
        ])->render();

        $this->assertStringContainsString('Ficha técnica', $html);
        $this->assertStringContainsString('Windows 11 Pro', $html);
        $this->assertStringContainsString('Sim — MAC 44:A3:BB:17:2E:8E', $html);
        $this->assertStringContainsString('Identificação interna:</strong> NURTIC121', $html);
        $this->assertStringContainsString('R$ 7.899,90', $html);
        $this->assertStringContainsString('Nota fiscal:</strong> 12345', $html);
    }
}
