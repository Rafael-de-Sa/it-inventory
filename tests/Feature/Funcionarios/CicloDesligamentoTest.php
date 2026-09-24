<?php

namespace Tests\Feature\Funcionarios;

use App\Models\Equipamento;
use App\Models\Funcionario;
use App\Models\Movimentacao;
use App\Models\MovimentacaoEquipamento;
use App\Models\Setor;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Ciclo completo pelas rotas: admissão → termo de responsabilidade → desligamento bloqueado →
 * devolução → termos assinados → desligamento concluído.
 */
class CicloDesligamentoTest extends TestCase
{
    use RefreshDatabase;

    public function test_ciclo_completo_do_desligamento(): void
    {
        Storage::fake('local');
        $this->autenticar();

        $setor = Setor::factory()->create();
        $equipamentos = Equipamento::factory()->count(2)->create();
        $admitidoEm = today()->subYear()->toDateString();

        // 1. Admissão
        $cpf = fake('pt_BR')->cpf(false);
        $this->post(route('funcionarios.store'), [
            'empresa_id' => $setor->empresa_id,
            'setor_id' => $setor->id,
            'nome' => 'Maria',
            'sobrenome' => 'Souza',
            'cpf' => $cpf,
            'matricula' => '4321',
            'admitido_em' => $admitidoEm,
            'terceirizado' => 0,
        ])->assertSessionHasNoErrors();

        $funcionario = Funcionario::where('cpf', $cpf)->firstOrFail();
        $this->assertSame($admitidoEm, $funcionario->admitido_em->toDateString());
        Usuario::factory()->create(['funcionario_id' => $funcionario->id]);

        // 2. Termo de responsabilidade com dois equipamentos
        $this->post(route('movimentacoes.store'), [
            'empresa_id' => $setor->empresa_id,
            'setor_id' => $setor->id,
            'funcionario_id' => $funcionario->id,
            'equipamentos' => $equipamentos->pluck('id')->all(),
        ])->assertSessionHasNoErrors();

        $termo = Movimentacao::where('funcionario_id', $funcionario->id)->sole();
        $equipamentos->each(fn (Equipamento $equipamento) => $this->assertSame('em_uso', $equipamento->refresh()->status));

        // 3. Desligamento bloqueado: equipamentos em uso
        $this->desligar($funcionario)->assertSessionHas('error', fn ($m) => str_contains($m, 'equipamentos sob responsabilidade'));

        // 4. Termo de responsabilidade assinado
        $this->post(route('movimentacoes.upload-termo-responsabilidade', $termo), ['arquivo_termo' => $this->pdf()])
            ->assertSessionHasNoErrors();
        $this->assertSame('concluida', $termo->refresh()->status);

        // Continua bloqueado enquanto os equipamentos não forem devolvidos
        $this->desligar($funcionario)->assertSessionHas('error', fn ($m) => str_contains($m, 'equipamentos sob responsabilidade'));

        // 5. Devolução: um para manutenção, outro devolvido normalmente
        [$paraManutencao, $devolvido] = $equipamentos->all();
        $this->post(route('movimentacoes.devolucao.store'), [
            'empresa_id' => $setor->empresa_id,
            'setor_id' => $setor->id,
            'funcionario_id' => $funcionario->id,
            'equipamentos' => [$paraManutencao->id, $devolvido->id],
            'motivos_devolucao_equipamentos' => [$paraManutencao->id => 'manutencao', $devolvido->id => 'devolucao'],
        ])->assertSessionHasNoErrors();

        $devolucao = Movimentacao::where('funcionario_id', $funcionario->id)
            ->where('tipo_movimentacao', Movimentacao::TIPO_DEVOLUCAO)->sole();

        $this->assertSame('em_manutencao', $paraManutencao->refresh()->status);
        $this->assertSame('disponivel', $devolvido->refresh()->status);
        $this->assertSame('encerrada', $termo->refresh()->status);
        $this->assertSame(2, MovimentacaoEquipamento::where('movimentacao_id', $termo->id)
            ->where('devolucao_movimentacao_id', $devolucao->id)->count());

        // 6. Desligamento bloqueado: termo de devolução ainda sem o arquivo assinado
        $this->desligar($funcionario)->assertSessionHas('error', fn ($m) => str_contains($m, 'termos de responsabilidade ou devolução pendentes'));

        // 7. Termo de devolução assinado
        $this->post(route('movimentacoes.upload-termo-devolucao', $devolucao), ['arquivo_termo' => $this->pdf()])
            ->assertSessionHasNoErrors();
        $this->assertSame('encerrada', $devolucao->refresh()->status);

        // 8. Desligamento concluído
        $this->desligar($funcionario)->assertSessionHas('success');

        $funcionario->refresh();
        $this->assertSame(today()->toDateString(), $funcionario->desligado_em->toDateString());
        $this->assertFalse((bool) $funcionario->ativo);
        $this->assertNull($funcionario->usuario()->first());

        // Linha do tempo dos equipamentos: cadastro, empréstimo e devolução
        $this->assertSame(
            ['cadastro', 'emprestimo', 'devolucao'],
            $paraManutencao->historicos()->orderBy('id')->pluck('evento')->all()
        );
    }

    private function desligar(Funcionario $funcionario)
    {
        return $this->post(route('funcionarios.desligar', $funcionario), ['desligado_em' => today()->toDateString()]);
    }

    private function pdf(): UploadedFile
    {
        return UploadedFile::fake()->create('termo-assinado.pdf', 120, 'application/pdf');
    }
}
