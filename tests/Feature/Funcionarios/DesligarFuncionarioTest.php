<?php

namespace Tests\Feature\Funcionarios;

use App\Models\Funcionario;
use App\Models\Movimentacao;
use App\Models\MovimentacaoEquipamento;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Registro de desligamento (issue #1): data informada, validações e pendências que bloqueiam.
 */
class DesligarFuncionarioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->autenticar();
    }

    private function desligar(Funcionario $funcionario, ?string $data)
    {
        return $this->from(route('funcionarios.show', $funcionario))
            ->post(route('funcionarios.desligar', $funcionario), ['desligado_em' => $data]);
    }

    public function test_desliga_funcionario_na_data_informada_e_remove_o_usuario_vinculado(): void
    {
        $funcionario = Funcionario::factory()->create();
        $usuario = Usuario::factory()->create(['funcionario_id' => $funcionario->id]);
        $data = today()->subDays(3)->toDateString();

        $this->desligar($funcionario, $data)
            ->assertRedirect(route('funcionarios.show', $funcionario))
            ->assertSessionHas('success');

        $funcionario->refresh();
        $this->assertSame($data, $funcionario->desligado_em->toDateString());
        $this->assertFalse((bool) $funcionario->ativo);
        $this->assertSoftDeleted($usuario);
    }

    public function test_aceita_desligamento_na_propria_data_de_admissao(): void
    {
        $funcionario = Funcionario::factory()->create(['admitido_em' => today()->subMonth()]);

        $this->desligar($funcionario, today()->subMonth()->toDateString())
            ->assertSessionHasNoErrors();

        $this->assertNotNull($funcionario->refresh()->desligado_em);
    }

    public function test_nao_aceita_data_futura(): void
    {
        $funcionario = Funcionario::factory()->create();

        $this->desligar($funcionario, today()->addDay()->toDateString())
            ->assertSessionHasErrors(['desligado_em' => 'A data de desligamento não pode ser futura.']);

        $this->assertNull($funcionario->refresh()->desligado_em);
    }

    public function test_nao_aceita_data_anterior_a_admissao(): void
    {
        $funcionario = Funcionario::factory()->create(['admitido_em' => today()->subMonth()]);

        $this->desligar($funcionario, today()->subMonths(2)->toDateString())
            ->assertSessionHasErrors('desligado_em');

        $this->assertNull($funcionario->refresh()->desligado_em);
    }

    public function test_exige_a_data_de_desligamento(): void
    {
        $funcionario = Funcionario::factory()->create();

        $this->desligar($funcionario, null)
            ->assertSessionHasErrors(['desligado_em' => 'Informe a data de desligamento.']);
    }

    public function test_exige_data_de_admissao_cadastrada(): void
    {
        $funcionario = Funcionario::factory()->create(['admitido_em' => null]);

        $this->desligar($funcionario, today()->toDateString())
            ->assertSessionHasErrors('desligado_em');

        $this->assertNull($funcionario->refresh()->desligado_em);
    }

    public function test_nao_desliga_funcionario_ja_desligado(): void
    {
        $dataOriginal = today()->subMonth()->toDateString();
        $funcionario = Funcionario::factory()->desligado($dataOriginal)->create();

        $this->desligar($funcionario, today()->toDateString())
            ->assertRedirect(route('funcionarios.show', $funcionario))
            ->assertSessionHas('error', 'Este funcionário já está marcado como desligado.');

        $this->assertSame($dataOriginal, $funcionario->refresh()->desligado_em->toDateString());
    }

    public function test_nao_desliga_com_equipamento_em_uso(): void
    {
        $funcionario = Funcionario::factory()->create();
        $termo = Movimentacao::factory()->encerrada()->create(['funcionario_id' => $funcionario->id]);
        MovimentacaoEquipamento::factory()->create(['movimentacao_id' => $termo->id]);

        $this->desligar($funcionario, today()->toDateString())
            ->assertSessionHas('error', fn (string $mensagem) => str_contains($mensagem, 'equipamentos sob responsabilidade'));

        $this->assertNull($funcionario->refresh()->desligado_em);
    }

    public function test_nao_desliga_com_termo_pendente_de_upload(): void
    {
        $funcionario = Funcionario::factory()->create();
        Movimentacao::factory()->create(['funcionario_id' => $funcionario->id]);

        $this->desligar($funcionario, today()->toDateString())
            ->assertSessionHas('error', fn (string $mensagem) => str_contains($mensagem, 'termos de responsabilidade ou devolução pendentes'));

        $this->assertNull($funcionario->refresh()->desligado_em);
    }

    public function test_termo_cancelado_nao_bloqueia_o_desligamento(): void
    {
        $funcionario = Funcionario::factory()->create();
        Movimentacao::factory()->create(['funcionario_id' => $funcionario->id, 'status' => 'cancelada']);

        $this->desligar($funcionario, today()->toDateString())
            ->assertSessionHas('success');
    }

    public function test_visitante_nao_autenticado_e_redirecionado_ao_login(): void
    {
        $funcionario = Funcionario::factory()->create();
        auth()->logout();

        $this->desligar($funcionario, today()->toDateString())
            ->assertRedirect(route('login'));

        $this->assertNull($funcionario->refresh()->desligado_em);
    }
}
