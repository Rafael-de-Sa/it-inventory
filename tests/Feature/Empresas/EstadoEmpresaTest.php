<?php

namespace Tests\Feature\Empresas;

use App\Models\Empresa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Estado (UF) da empresa como lista de seleção (issue #7).
 */
class EstadoEmpresaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->autenticar();
    }

    /** Dados válidos de cadastro, a partir da factory. */
    private function dadosEmpresa(array $sobrescrever = []): array
    {
        return array_merge(
            Empresa::factory()->make()->only([
                'nome_fantasia', 'razao_social', 'cnpj', 'cep', 'logradouro', 'numero', 'bairro', 'cidade', 'email',
            ]),
            ['estado' => 'PR'],
            $sobrescrever,
        );
    }

    public function test_formulario_de_cadastro_lista_as_27_ufs(): void
    {
        $resposta = $this->get(route('empresas.create'))->assertOk();

        $select = (string) str($resposta->getContent())->after('<select id="estado" name="estado"')->before('</select>');

        $this->assertMatchesRegularExpression('/<option value="PR"\s*>PR — Paraná<\/option>/', $select);
        $this->assertMatchesRegularExpression('/<option value="DF"\s*>DF — Distrito Federal<\/option>/', $select);
        $this->assertSame(27, substr_count($select, '<option value="') - 1); // desconta o "Selecione..."
    }

    public function test_cadastra_empresa_com_a_uf_selecionada(): void
    {
        $dados = $this->dadosEmpresa(['estado' => 'MS']);

        $this->post(route('empresas.store'), $dados)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('empresas.index'));

        $this->assertDatabaseHas('empresas', ['cnpj' => $dados['cnpj'], 'estado' => 'MS']);
    }

    public function test_uf_em_minusculas_e_salva_em_maiusculas(): void
    {
        $dados = $this->dadosEmpresa(['estado' => 'df']);

        $this->post(route('empresas.store'), $dados)->assertSessionHasNoErrors();

        $this->assertDatabaseHas('empresas', ['cnpj' => $dados['cnpj'], 'estado' => 'DF']);
    }

    public function test_rejeita_uf_inexistente(): void
    {
        $dados = $this->dadosEmpresa(['estado' => 'XX']);

        $this->post(route('empresas.store'), $dados)
            ->assertSessionHasErrors(['estado' => 'UF inválida.']);

        $this->assertDatabaseMissing('empresas', ['cnpj' => $dados['cnpj']]);
    }

    public function test_exige_a_uf(): void
    {
        $this->post(route('empresas.store'), $this->dadosEmpresa(['estado' => '']))
            ->assertSessionHasErrors('estado');
    }

    public function test_edicao_vem_com_a_uf_da_empresa_selecionada(): void
    {
        $empresa = Empresa::factory()->create(['estado' => 'SC']);

        $html = $this->get(route('empresas.edit', $empresa))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<option value="SC"\s+selected\s*>SC — Santa Catarina<\/option>/', $html);
    }
}
