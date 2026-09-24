<?php

namespace Database\Factories;

use App\Models\Funcionario;
use App\Models\Movimentacao;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Termo de responsabilidade pendente (sem o termo assinado enviado). O setor acompanha o do funcionário.
 *
 * @extends Factory<Movimentacao>
 */
class MovimentacaoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'funcionario_id' => Funcionario::factory(),
            'setor_id' => fn (array $atributos) => Funcionario::find($atributos['funcionario_id'])->setor_id,
            'tipo_movimentacao' => Movimentacao::TIPO_RESPONSABILIDADE,
            'status' => 'pendente',
            'observacao' => null,
            'termo_responsabilidade' => null,
        ];
    }

    public function devolucao(): static
    {
        return $this->state(['tipo_movimentacao' => Movimentacao::TIPO_DEVOLUCAO]);
    }

    /** Termo assinado já enviado. */
    public function encerrada(): static
    {
        return $this->state(fn (array $atributos) => $atributos['tipo_movimentacao'] === Movimentacao::TIPO_DEVOLUCAO
            ? ['status' => 'encerrada', 'termo_devolucao' => 'termos/devolucao-teste.pdf']
            : ['status' => 'encerrada', 'termo_responsabilidade' => 'termos/responsabilidade-teste.pdf']);
    }
}
