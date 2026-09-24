<?php

namespace App\Http\Requests\Ocorrencias;

use App\Support\Mask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Regras comuns ao registro e à edição de ocorrência. O equipamento só é informado no registro.
 */
class OcorrenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $texto = fn ($valor) => filled($valor) ? (string) str($valor)->squish() : null;

        $this->merge([
            'problema' => $texto($this->input('problema')),
            'canal' => $texto($this->input('canal')),
            'protocolo' => $texto($this->input('protocolo')),
            'valor_cobrado' => Mask::decimal($this->input('valor_cobrado')),
        ]);
    }

    public function rules(): array
    {
        return [
            'funcionario_id' => ['nullable', 'integer', Rule::exists('funcionarios', 'id')],
            'reportado_em' => ['required', 'date', 'before_or_equal:today'],
            'data_problema' => ['nullable', 'date', 'before_or_equal:reportado_em'],
            'problema' => ['required', 'string', 'max:255'],
            'previsao_em' => ['nullable', 'date', 'after_or_equal:reportado_em'],
            'liberado_em' => ['nullable', 'date', 'after_or_equal:reportado_em', 'before_or_equal:today'],
            'solucao' => ['nullable', 'required_with:liberado_em', 'string', 'max:2000'],
            'canal' => ['nullable', 'string', 'max:30'],
            'protocolo' => ['nullable', 'string', 'max:50'],
            'valor_cobrado' => ['nullable', 'numeric', 'decimal:0,2', 'between:0,99999999.99'],
            'observacao' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'data_problema.before_or_equal' => 'A data do problema não pode ser posterior à data em que foi reportado.',
            'previsao_em.after_or_equal' => 'A previsão não pode ser anterior à data em que o problema foi reportado.',
            'liberado_em.after_or_equal' => 'A liberação não pode ser anterior à data em que o problema foi reportado.',
            'liberado_em.before_or_equal' => 'A data de liberação não pode ser futura.',
            'reportado_em.before_or_equal' => 'A data em que o problema foi reportado não pode ser futura.',
            'solucao.required_with' => 'Informe a solução ao liberar o equipamento.',
            'valor_cobrado.numeric' => 'Informe o valor apenas com números (ex.: 150,00).',
        ];
    }

    public function attributes(): array
    {
        return [
            'equipamento_id' => 'equipamento',
            'funcionario_id' => 'último usuário',
            'reportado_em' => 'reportado em',
            'data_problema' => 'data do problema',
            'problema' => 'problema',
            'previsao_em' => 'previsão',
            'liberado_em' => 'liberado em',
            'solucao' => 'solução',
            'canal' => 'canal',
            'protocolo' => 'protocolo',
            'valor_cobrado' => 'valor cobrado do colaborador',
            'observacao' => 'observação',
        ];
    }
}
