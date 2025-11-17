<?php

namespace App\Http\Requests\Movimentacoes;

use App\Models\Equipamento;
use App\Models\Funcionario;
use App\Models\Setor;
use Illuminate\Foundation\Http\FormRequest;

class StoreMovimentacaoRequest extends FormRequest
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
            'empresa_id'     => ['required', 'integer', 'exists:empresas,id'],
            'setor_id'       => ['required', 'integer', 'exists:setores,id'],
            'funcionario_id' => ['required', 'integer', 'exists:funcionarios,id'],

            'observacao'     => ['nullable', 'string', 'max:2000'],

            'equipamentos'   => ['required', 'array', 'min:1'],
            'equipamentos.*' => ['integer', 'distinct', 'exists:equipamentos,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'equipamentos.required' => 'Selecione ao menos um equipamento.',
            'equipamentos.min'      => 'Selecione ao menos um equipamento.',
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

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $empresaId       = $this->input('empresa_id');
            $setorId         = $this->input('setor_id');
            $funcionarioId   = $this->input('funcionario_id');
            $idsEquipamentos = $this->input('equipamentos', []);

            // setor precisa pertencer à empresa
            if ($empresaId && $setorId) {
                $setorValido = Setor::query()
                    ->where('id', $setorId)
                    ->where('empresa_id', $empresaId)
                    ->where('ativo', true)
                    ->whereNull('apagado_em')
                    ->exists();

                if (! $setorValido) {
                    $validator->errors()->add(
                        'setor_id',
                        'O setor selecionado não pertence à empresa informada.'
                    );
                }
            }

            // funcionário precisa pertencer ao setor
            if ($setorId && $funcionarioId) {
                $funcionarioValido = Funcionario::query()
                    ->where('id', $funcionarioId)
                    ->where('setor_id', $setorId)
                    ->where('ativo', true)
                    ->whereNull('desligado_em')
                    ->whereNull('apagado_em')
                    ->exists();

                if (! $funcionarioValido) {
                    $validator->errors()->add(
                        'funcionario_id',
                        'O funcionário selecionado não pertence ao setor informado ou não está ativo.'
                    );
                }
            }

            // equipamentos precisam estar ativos e disponíveis
            if (! empty($idsEquipamentos)) {
                $quantidadeEsperada = count($idsEquipamentos);

                $quantidadeDisponiveis = Equipamento::query()
                    ->whereIn('id', $idsEquipamentos)
                    ->where('ativo', true)
                    ->whereNull('apagado_em')
                    ->where('status', 'disponivel')
                    ->count();

                if ($quantidadeDisponiveis !== $quantidadeEsperada) {
                    $validator->errors()->add(
                        'equipamentos',
                        'Um ou mais equipamentos selecionados não estão disponíveis para movimentação.'
                    );
                }
            }
        });
    }
}
