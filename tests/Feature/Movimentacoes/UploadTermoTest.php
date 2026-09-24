<?php

namespace Tests\Feature\Movimentacoes;

use App\Models\Movimentacao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Envio dos termos assinados: situação resultante e bloqueios de reenvio.
 */
class UploadTermoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->autenticar();
    }

    private function enviarResponsabilidade(Movimentacao $movimentacao)
    {
        return $this->post(route('movimentacoes.upload-termo-responsabilidade', $movimentacao), [
            'arquivo_termo' => UploadedFile::fake()->create('termo.pdf', 50, 'application/pdf'),
        ]);
    }

    private function enviarDevolucao(Movimentacao $movimentacao)
    {
        return $this->post(route('movimentacoes.upload-termo-devolucao', $movimentacao), [
            'arquivo_termo' => UploadedFile::fake()->create('termo.pdf', 50, 'application/pdf'),
        ]);
    }

    public function test_termo_de_responsabilidade_pendente_passa_a_concluida(): void
    {
        $movimentacao = Movimentacao::factory()->create();

        $this->enviarResponsabilidade($movimentacao)->assertSessionHasNoErrors();

        $movimentacao->refresh();
        $this->assertSame('concluida', $movimentacao->status);
        Storage::disk('local')->assertExists($movimentacao->termo_responsabilidade);
    }

    public function test_envio_depois_da_devolucao_mantem_a_movimentacao_encerrada(): void
    {
        $movimentacao = Movimentacao::factory()->create(['status' => 'encerrada']);

        $this->enviarResponsabilidade($movimentacao)->assertSessionHasNoErrors();

        $movimentacao->refresh();
        $this->assertSame('encerrada', $movimentacao->status);
        $this->assertNotNull($movimentacao->termo_responsabilidade);
    }

    public function test_nao_permite_reenviar_o_termo_de_responsabilidade(): void
    {
        $movimentacao = Movimentacao::factory()->encerrada()->create();
        $arquivoOriginal = $movimentacao->termo_responsabilidade;

        $this->enviarResponsabilidade($movimentacao)
            ->assertSessionHasErrors(['arquivo_termo' => 'O termo de responsabilidade desta movimentação já foi enviado.']);

        $this->assertSame($arquivoOriginal, $movimentacao->refresh()->termo_responsabilidade);
    }

    public function test_nao_envia_termo_de_movimentacao_cancelada(): void
    {
        $movimentacao = Movimentacao::factory()->create(['status' => 'cancelada']);

        $this->enviarResponsabilidade($movimentacao)->assertSessionHasErrors('arquivo_termo');

        $this->assertNull($movimentacao->refresh()->termo_responsabilidade);
    }

    public function test_nao_envia_termo_de_responsabilidade_em_uma_devolucao(): void
    {
        $devolucao = Movimentacao::factory()->devolucao()->create();

        $this->enviarResponsabilidade($devolucao)
            ->assertSessionHasErrors(['arquivo_termo' => 'Esta movimentação não é um termo de responsabilidade nem de troca.']);
    }

    public function test_termo_de_devolucao_encerra_a_devolucao(): void
    {
        $devolucao = Movimentacao::factory()->devolucao()->create();

        $this->enviarDevolucao($devolucao)->assertSessionHasNoErrors();

        $devolucao->refresh();
        $this->assertSame('encerrada', $devolucao->status);
        Storage::disk('local')->assertExists($devolucao->termo_devolucao);
    }

    public function test_nao_permite_reenviar_o_termo_de_devolucao(): void
    {
        $devolucao = Movimentacao::factory()->devolucao()->encerrada()->create();

        $this->enviarDevolucao($devolucao)
            ->assertSessionHasErrors(['arquivo_termo' => 'O termo de devolução desta movimentação já foi enviado.']);
    }

    public function test_nao_envia_termo_de_devolucao_em_um_termo_de_responsabilidade(): void
    {
        $movimentacao = Movimentacao::factory()->create();

        $this->enviarDevolucao($movimentacao)
            ->assertSessionHasErrors(['arquivo_termo' => 'Esta movimentação não é uma devolução.']);

        $this->assertNull($movimentacao->refresh()->termo_devolucao);
    }

    public function test_exige_arquivo_pdf(): void
    {
        $movimentacao = Movimentacao::factory()->create();

        $this->post(route('movimentacoes.upload-termo-responsabilidade', $movimentacao), [
            'arquivo_termo' => UploadedFile::fake()->create('termo.docx', 50),
        ])->assertSessionHasErrors('arquivo_termo');
    }
}
