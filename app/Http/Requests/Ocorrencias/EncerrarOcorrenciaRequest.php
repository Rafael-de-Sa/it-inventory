<?php

namespace App\Http\Requests\Ocorrencias;

use App\Models\Ocorrencia;
use App\Support\Mask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Encerramento da ocorrência: data de liberação pela TI, solução e custos.
 */
class EncerrarOcorrenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'solucao' => filled($this->input('solucao')) ? trim($this->input('solucao')) : null,
            'fornecedor' => filled($this->input('fornecedor')) ? (string) str($this->input('fornecedor'))->squish() : null,
            'custo_manutencao' => Mask::decimal($this->input('custo_manutencao')),
            'valor_cobrado' => Mask::decimal($this->input('valor_cobrado')),
        ]);
    }

    public function rules(): array
    {
        /** @var Ocorrencia $ocorrencia */
        $ocorrencia = $this->route('ocorrencia');

        return [
            'liberado_em' => ['required', 'date', 'after_or_equal:' . $ocorrencia->reportado_em->format('Y-m-d'), 'before_or_equal:today'],
            'solucao' => ['required', 'string', 'max:2000'],
            'custo_manutencao' => ['nullable', 'numeric', 'decimal:0,2', 'between:0,99999999.99'],
            'fornecedor' => ['nullable', 'string', 'max:80'],
            'valor_cobrado' => ['nullable', 'numeric', 'decimal:0,2', 'between:0,99999999.99'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (! $this->route('ocorrencia')->estaAberta()) {
                    $validator->errors()->add('liberado_em', 'Esta ocorrência já está encerrada.');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'liberado_em.after_or_equal' => 'A liberação não pode ser anterior à data em que o problema foi reportado.',
            'liberado_em.before_or_equal' => 'A data de liberação não pode ser futura.',
            'custo_manutencao.numeric' => 'Informe o custo apenas com números (ex.: 350,00).',
            'valor_cobrado.numeric' => 'Informe o valor apenas com números (ex.: 150,00).',
        ];
    }

    public function attributes(): array
    {
        return [
            'liberado_em' => 'liberado em',
            'solucao' => 'solução',
            'custo_manutencao' => 'custo da manutenção',
            'fornecedor' => 'fornecedor / assistência técnica',
            'valor_cobrado' => 'valor cobrado do colaborador',
        ];
    }
}
