<?php

namespace App\Http\Requests\Movimentacoes;

use App\Models\Funcionario;
use App\Models\Movimentacao;
use App\Models\MovimentacaoEquipamento;
use App\Models\Setor;
use Illuminate\Foundation\Http\FormRequest;

class StoreDevolucaoMovimentacaoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    protected function prepareForValidation(): void
    {
        $idsEquipamentos = $this->input('equipamentos', []);

        if (! is_array($idsEquipamentos)) {
            $idsEquipamentos = [];
        }

        $idsEquipamentos = array_values(array_unique(array_filter($idsEquipamentos, function ($valor) {
            return is_numeric($valor);
        })));

        $this->merge([
            'empresa_id'     => $this->input('empresa_id') ? (int) $this->input('empresa_id') : null,
            'setor_id'       => $this->input('setor_id') ? (int) $this->input('setor_id') : null,
            'funcionario_id' => $this->input('funcionario_id') ? (int) $this->input('funcionario_id') : null,
            'equipamentos'   => $idsEquipamentos,
        ]);
    }
    public function rules(): array
    {
        return [
            'empresa_id' => ['required', 'integer', 'exists:empresas,id'],
            'setor_id' => ['required', 'integer', 'exists:setores,id'],
            'funcionario_id' => ['required', 'integer', 'exists:funcionarios,id'],

            'observacao' => ['nullable', 'string', 'max:2000'],

            'equipamentos' => ['required', 'array', 'min:1'],
            'equipamentos.*' => ['integer', 'distinct', 'exists:equipamentos,id'],

            'observacoes_equipamentos' => ['nullable', 'array'],
            'observacoes_equipamentos.*' => ['nullable', 'string', 'max:2000'],

            'motivo_devolucao' => ['nullable', 'string', 'in:manutencao,defeito,quebra,devolucao,cancelada'],
        ];
    }
    public function attributes(): array
    {
        return [
            'empresa_id'     => 'empresa',
            'setor_id'       => 'setor',
            'funcionario_id' => 'funcionário',
            'observacao'     => 'observação',
            'equipamentos'   => 'equipamentos',
        ];
    }

    /**
     * Validações adicionais após as regras básicas.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $funcionarioId   = $this->input('funcionario_id');
            $idsEquipamentos = $this->input('equipamentos', []);

            if ($funcionarioId && ! empty($idsEquipamentos)) {
                $quantidadeEsperada = count($idsEquipamentos);

                $quantidadeEmUsoPeloFuncionario = MovimentacaoEquipamento::query()
                    ->whereIn('equipamento_id', $idsEquipamentos)
                    ->whereNull('devolvido_em')
                    ->whereHas('movimentacao', function ($query) use ($funcionarioId) {
                        $query
                            ->where('funcionario_id', $funcionarioId)
                            ->where('tipo_movimentacao', Movimentacao::TIPO_RESPONSABILIDADE)
                            ->where('status', '!=', 'cancelada');
                    })
                    ->count();

                if ($quantidadeEmUsoPeloFuncionario !== $quantidadeEsperada) {
                    $validator->errors()->add(
                        'equipamentos',
                        'Um ou mais equipamentos selecionados não estão mais em responsabilidade em aberto para este funcionário.'
                    );
                }
            }
        });
    }
}
