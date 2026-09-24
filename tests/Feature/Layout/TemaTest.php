<?php

namespace Tests\Feature\Layout;

use App\Models\Equipamento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tema claro/escuro (issue #3): botão de troca em todas as telas, tema aplicado antes da pintura
 * e paginação com os tokens de cor do projeto.
 */
class TemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_tem_o_botao_de_tema_e_aplica_o_tema_antes_da_pintura(): void
    {
        $html = $this->get(route('login'))->assertOk()->getContent();

        $this->assertStringContainsString('data-tema-alternar', $html);
        foreach (['claro', 'escuro', 'sistema'] as $tema) {
            $this->assertStringContainsString("data-tema-icone=\"{$tema}\"", $html);
        }

        // O script inline vem antes do CSS, para não piscar o tema claro.
        $this->assertLessThan(
            strpos($html, 'build/assets/app') ?: strpos($html, 'resources/css/app.css'),
            strpos($html, "localStorage.getItem('tema')"),
        );
    }

    public function test_telas_autenticadas_tem_o_botao_de_tema(): void
    {
        $this->autenticar();

        $this->get(route('equipamentos.index'))->assertOk()->assertSee('data-tema-alternar', false);
    }

    public function test_paginacao_usa_os_tokens_de_cor(): void
    {
        $this->autenticar();
        Equipamento::factory()->count(26)->create();

        $html = $this->get(route('equipamentos.index'))->assertOk()->getContent();
        $paginacao = (string) str($html)->after('aria-label="Pagination Navigation"')->before('</nav>');

        $this->assertStringContainsString('bg-surface', $paginacao);
        $this->assertStringContainsString('aria-current="page"', $paginacao);
        $this->assertStringNotContainsString('gray-', $paginacao);
    }
}
