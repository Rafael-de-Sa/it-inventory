<?php

namespace Tests\Feature\Empresas;

use App\Models\Empresa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Validações do cadastro de empresa: CNPJ (dígitos verificadores e unicidade), CEP, telefone
 * e a consulta de endereço pelo ViaCEP.
 */
class ValidacaoEmpresaTest extends TestCase
{
    use RefreshDatabase;

    private const CNPJ_VALIDO = '11222333000181';

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
                'nome_fantasia', 'razao_social', 'cep', 'logradouro', 'numero', 'bairro', 'cidade', 'estado', 'email',
            ]),
            ['cnpj' => self::CNPJ_VALIDO],
            $sobrescrever,
        );
    }

    public function test_cnpj_e_cep_com_mascara_sao_gravados_so_com_digitos(): void
    {
        $this->post(route('empresas.store'), $this->dadosEmpresa([
            'cnpj' => '11.222.333/0001-81',
            'cep' => '87.020-025',
            'telefone' => '(44) 99999-8888',
        ]))->assertSessionHasNoErrors()->assertRedirect(route('empresas.index'));

        $this->assertDatabaseHas('empresas', [
            'cnpj' => self::CNPJ_VALIDO,
            'cep' => '87020025',
            'telefone' => '44999998888',
        ]);
    }

    public static function cnpjsInvalidos(): array
    {
        return [
            'primeiro dígito verificador errado' => ['11222333000191', 'Informe um CNPJ válido.'],
            'segundo dígito verificador errado' => ['11222333000182', 'Informe um CNPJ válido.'],
            'todos os dígitos iguais' => ['11111111111111', 'Informe um CNPJ válido.'],
            'dígitos a menos' => ['1122233300018', 'O CNPJ deve conter exatamente 14 dígitos.'],
        ];
    }

    #[DataProvider('cnpjsInvalidos')]
    public function test_rejeita_cnpj_invalido(string $cnpj, string $mensagem): void
    {
        $this->post(route('empresas.store'), $this->dadosEmpresa(['cnpj' => $cnpj]))
            ->assertSessionHasErrors(['cnpj' => $mensagem]);

        $this->assertDatabaseMissing('empresas', ['cnpj' => $cnpj]);
    }

    public function test_rejeita_cnpj_ja_cadastrado_mesmo_com_mascara(): void
    {
        Empresa::factory()->create(['cnpj' => self::CNPJ_VALIDO]);

        $this->post(route('empresas.store'), $this->dadosEmpresa(['cnpj' => '11.222.333/0001-81']))
            ->assertSessionHasErrors(['cnpj' => 'Há uma outra empresa cadastrada com o mesmo CNPJ']);
    }

    public function test_edicao_mantem_o_proprio_cnpj_e_bloqueia_o_de_outra_empresa(): void
    {
        $empresa = Empresa::factory()->create(['cnpj' => self::CNPJ_VALIDO]);
        $outra = Empresa::factory()->create();

        $dados = $this->dadosEmpresa(['ativo' => 1]);

        $this->put(route('empresas.update', $empresa), $dados)->assertSessionHasNoErrors();

        $this->put(route('empresas.update', $outra), $dados)
            ->assertSessionHasErrors(['cnpj' => 'Há uma outra empresa cadastrada com o mesmo CNPJ']);
    }

    public static function cepsInvalidos(): array
    {
        return [
            'dígitos a menos' => ['8702002'],
            'dígitos a mais' => ['870200250'],
            'vazio' => [''],
        ];
    }

    #[DataProvider('cepsInvalidos')]
    public function test_rejeita_cep_invalido(string $cep): void
    {
        $this->post(route('empresas.store'), $this->dadosEmpresa(['cep' => $cep]))
            ->assertSessionHasErrors('cep');
    }

    public function test_telefone_precisa_do_ddd(): void
    {
        $this->post(route('empresas.store'), $this->dadosEmpresa(['telefone' => '99998888']))
            ->assertSessionHasErrors(['telefone' => 'Informe um telefone com DDD (10 ou 11 dígitos).']);
    }

    public function test_consulta_de_cep_devolve_o_endereco(): void
    {
        Http::fake(['viacep.com.br/*' => Http::response([
            'cep' => '87020-025',
            'logradouro' => 'Avenida Brasil',
            'bairro' => 'Zona 01',
            'localidade' => 'Maringá',
            'uf' => 'pr',
        ])]);

        $this->getJson(route('empresas.cep', '87020-025'))
            ->assertOk()
            ->assertExactJson([
                'cep' => '87020025',
                'logradouro' => 'Avenida Brasil',
                'bairro' => 'Zona 01',
                'cidade' => 'Maringá',
                'estado' => 'PR',
            ]);

        Http::assertSent(fn ($requisicao) => str_ends_with($requisicao->url(), '/87020025/json/'));
    }

    public function test_consulta_de_cep_inexistente(): void
    {
        Http::fake(['viacep.com.br/*' => Http::response(['erro' => true])]);

        $this->getJson(route('empresas.cep', '99999999'))
            ->assertNotFound()
            ->assertJson(['message' => 'CEP não encontrado.']);
    }

    public function test_consulta_de_cep_com_formato_invalido_nao_chama_o_servico(): void
    {
        Http::fake();

        $this->getJson(route('empresas.cep', '123'))->assertStatus(422);

        Http::assertNothingSent();
    }

    public function test_consulta_de_cep_com_servico_fora_do_ar(): void
    {
        Http::fake(['viacep.com.br/*' => Http::response(null, 503)]);

        $this->getJson(route('empresas.cep', '87020025'))
            ->assertStatus(502)
            ->assertJson(['message' => 'Falha ao consultar serviço de CEP.']);
    }
}
