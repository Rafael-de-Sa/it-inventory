<?php

namespace Tests\Feature\Perfis;

use App\Enums\Perfil;
use App\Models\Equipamento;
use App\Models\Funcionario;
use App\Models\Movimentacao;
use App\Models\Setor;
use App\Models\Usuario;
use Database\Seeders\UsuarioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Perfis de acesso (issue #4): TIC acessa tudo; Departamento Pessoal só a área de funcionários.
 */
class PerfisAcessoTest extends TestCase
{
    use RefreshDatabase;

    private function entrarComoDp(): Usuario
    {
        return $this->autenticar(Usuario::factory()->departamentoPessoal()->create());
    }

    /** Rotas (GET) fora da área do DP. */
    public static function rotasRestritasAoTic(): array
    {
        return [
            'empresas' => ['empresas.index'],
            'setores' => ['setores.index'],
            'tipos de equipamento' => ['tipo-equipamentos.index'],
            'equipamentos' => ['equipamentos.index'],
            'usuarios' => ['usuarios.index'],
            'movimentacoes' => ['movimentacoes.index'],
            'novo termo' => ['movimentacoes.create'],
            'devolucao' => ['movimentacoes.devolucao.create'],
        ];
    }

    #[DataProvider('rotasRestritasAoTic')]
    public function test_dp_recebe_403_fora_da_area_de_funcionarios(string $rota): void
    {
        $this->entrarComoDp();

        $this->get(route($rota))
            ->assertForbidden()
            ->assertSee('Acesso não permitido');
    }

    #[DataProvider('rotasRestritasAoTic')]
    public function test_tic_acessa_todas_as_areas(string $rota): void
    {
        $this->autenticar();

        $this->get(route($rota))->assertOk();
    }

    public function test_dp_lista_consulta_cadastra_e_edita_funcionarios(): void
    {
        $this->entrarComoDp();
        $setor = Setor::factory()->create();
        $funcionario = Funcionario::factory()->create(['setor_id' => $setor->id]);

        $this->get(route('funcionarios.index'))->assertOk();
        $this->get(route('funcionarios.create'))->assertOk();
        $this->get(route('funcionarios.show', $funcionario))->assertOk();
        $this->get(route('funcionarios.edit', $funcionario))->assertOk();
        $this->getJson(route('funcionarios.setoresPorEmpresa', $setor->empresa_id))->assertOk();

        $cpf = fake('pt_BR')->cpf(false);
        $this->post(route('funcionarios.store'), [
            'empresa_id' => $setor->empresa_id,
            'setor_id' => $setor->id,
            'nome' => 'Joana',
            'sobrenome' => 'Lima',
            'cpf' => $cpf,
            'matricula' => '7788',
            'admitido_em' => today()->subMonth()->toDateString(),
            'terceirizado' => 0,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('funcionarios', ['cpf' => $cpf]);
    }

    public function test_dp_gera_o_relatorio_de_equipamentos_do_funcionario(): void
    {
        $this->entrarComoDp();
        $funcionario = Funcionario::factory()->create();

        $this->get(route('relatorios.funcionarios.equipamentos', $funcionario))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_dp_registra_desligamento(): void
    {
        $this->entrarComoDp();
        $funcionario = Funcionario::factory()->create();

        $this->post(route('funcionarios.desligar', $funcionario), ['desligado_em' => today()->toDateString()])
            ->assertSessionHas('success');

        $this->assertNotNull($funcionario->refresh()->desligado_em);
    }

    public function test_dp_nao_exclui_funcionario(): void
    {
        $this->entrarComoDp();
        $funcionario = Funcionario::factory()->create();

        $this->delete(route('funcionarios.destroy', $funcionario))->assertForbidden();

        $this->assertNotSoftDeleted($funcionario);
    }

    public function test_dp_nao_registra_devolucao_nem_ve_historico_de_equipamento(): void
    {
        $this->entrarComoDp();
        $funcionario = Funcionario::factory()->create();

        $this->post(route('movimentacoes.devolucao.store'), [
            'empresa_id' => $funcionario->setor->empresa_id,
            'setor_id' => $funcionario->setor_id,
            'funcionario_id' => $funcionario->id,
            'equipamentos' => [Equipamento::factory()->emUso()->create()->id],
        ])->assertForbidden();

        $this->get(route('relatorios.equipamentos.historico', Equipamento::factory()->create()))->assertForbidden();
        $this->assertSame(0, Movimentacao::count());
    }

    public function test_menu_do_dp_mostra_apenas_dashboard_e_funcionarios(): void
    {
        $this->entrarComoDp();

        $html = $this->get(route('funcionarios.index'))->assertOk()->getContent();
        $menu = (string) str($html)->between('<header', '</header>');

        $this->assertStringContainsString(route('funcionarios.index'), $menu);
        $this->assertStringContainsString('Dashboard', $menu);
        foreach (['empresas.index', 'equipamentos.index', 'usuarios.index', 'movimentacoes.index'] as $rota) {
            $this->assertStringNotContainsString(route($rota), $menu);
        }
        $this->assertStringNotContainsString('Operações', $menu);
    }

    public function test_dp_com_pendencia_ve_orientacao_para_procurar_a_tic_e_nao_ve_excluir(): void
    {
        $this->entrarComoDp();
        $funcionario = Funcionario::factory()->create();
        Movimentacao::factory()->create(['funcionario_id' => $funcionario->id]);

        $this->get(route('funcionarios.show', $funcionario))
            ->assertOk()
            ->assertSee('Encaminhe à equipe de TIC')
            ->assertDontSee('name="_method" value="DELETE"', false);
    }

    public function test_tic_cadastra_usuario_com_perfil(): void
    {
        $this->autenticar();
        $funcionario = Funcionario::factory()->create();

        $this->post(route('usuarios.store'), [
            'funcionario_id' => $funcionario->id,
            'email' => 'novo.dp@empresa.com.br',
            'email_confirmation' => 'novo.dp@empresa.com.br',
            'senha' => 'Senha@123',
            'senha_confirmation' => 'Senha@123',
            'perfil' => 'dp',
        ])->assertSessionHasNoErrors();

        $this->assertSame(Perfil::DP, Usuario::where('email', 'novo.dp@empresa.com.br')->sole()->perfil);
    }

    public function test_cadastro_de_usuario_exige_perfil_valido(): void
    {
        $this->autenticar();

        $this->post(route('usuarios.store'), ['perfil' => 'gerente'])->assertSessionHasErrors('perfil');
    }

    public function test_tic_altera_o_perfil_de_outro_usuario(): void
    {
        $this->autenticar();
        $outro = Usuario::factory()->departamentoPessoal()->create();

        $this->put(route('usuarios.update', $outro), [
            'email' => $outro->email,
            'email_confirmation' => $outro->email,
            'ativo' => 1,
            'perfil' => 'tic',
        ])->assertSessionHasNoErrors();

        $this->assertSame(Perfil::TIC, $outro->refresh()->perfil);
    }

    public function test_usuario_nao_altera_o_proprio_perfil(): void
    {
        $eu = $this->autenticar();

        $this->put(route('usuarios.update', $eu), [
            'email' => $eu->email,
            'email_confirmation' => $eu->email,
            'ativo' => 1,
            'perfil' => 'dp',
        ])->assertSessionHasNoErrors();

        $this->assertSame(Perfil::TIC, $eu->refresh()->perfil);
    }

    public function test_seeder_cria_usuario_do_dp_com_senha_123456(): void
    {
        // Funcionário do admin (mesmo CPF do FuncionarioSeeder) e o setor do DP.
        $setorTi = Setor::factory()->create(['nome' => 'Tecnologia da Informação']);
        Setor::factory()->create(['nome' => 'Departamento Pessoal', 'empresa_id' => $setorTi->empresa_id]);
        Funcionario::factory()->create(['setor_id' => $setorTi->id, 'cpf' => '38421835009']);

        $this->seed(UsuarioSeeder::class);
        $this->seed(UsuarioSeeder::class); // pode rodar de novo sem duplicar

        $dp = Usuario::where('email', 'dp@gmail.com')->sole();
        $admin = Usuario::where('email', 'admin@gmail.com')->sole();

        $this->assertSame(Perfil::DP, $dp->perfil);
        $this->assertSame(Perfil::TIC, $admin->perfil);
        $this->assertTrue(Hash::check('123456', $dp->senha));
        $this->assertSame('Departamento Pessoal', $dp->funcionario->setor->nome);
    }
}
