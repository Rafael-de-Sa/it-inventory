<?php

namespace App\Http\Requests\Funcionarios;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class DesligarFuncionarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * A data de desligamento não pode ser futura nem anterior à admissão.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $admitidoEm = $this->route('funcionario')?->admitido_em;

        return [
            'desligado_em' => array_filter([
                'required',
                'date',
                'before_or_equal:today',
                $admitidoEm ? 'after_or_equal:' . $admitidoEm->toDateString() : null,
            ]),
        ];
    }

    /**
     * Funcionários cadastrados antes do campo de admissão precisam tê-lo preenchido antes do desligamento.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (! $this->route('funcionario')?->admitido_em) {
                    $validator->errors()->add(
                        'desligado_em',
                        'Informe a data de admissão no cadastro do funcionário antes de registrar o desligamento.'
                    );
                }
            },
        ];
    }

    public function messages(): array
    {
        $admitidoEm = $this->route('funcionario')?->admitido_em?->format('d/m/Y');

        return [
            'desligado_em.required' => 'Informe a data de desligamento.',
            'desligado_em.before_or_equal' => 'A data de desligamento não pode ser futura.',
            'desligado_em.after_or_equal' => "A data de desligamento não pode ser anterior à admissão ({$admitidoEm}).",
        ];
    }

    public function attributes(): array
    {
        return [
            'desligado_em' => 'data de desligamento',
        ];
    }
}
