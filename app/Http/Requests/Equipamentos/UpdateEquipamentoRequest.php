<?php

namespace App\Http\Requests\Equipamentos;

use App\Models\Equipamento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateEquipamentoRequest extends FormRequest
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
    public function rules(): array
    {
        $id = $this->route('equipamento')?->id ?? $this->route('id');

        return [
            'tipo_equipamento_id' => ['required', 'integer', 'exists:tipo_equipamentos,id'],
            'data_compra' => ['nullable', 'date'],
            'valor_compra' => ['nullable', 'numeric', 'between:0,9999999999.99'],
            'status' => ['required', Rule::in(array_keys(Equipamento::STATUS))],
            'descricao' => ['required', 'string', 'max:65535'],
            'patrimonio' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('equipamentos', 'patrimonio')
                    ->ignore($id)
                    ->whereNull('apagado_em'),
            ],
            'numero_serie' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('equipamentos', 'numero_serie')
                    ->ignore($id)
                    ->whereNull('apagado_em'),
            ],
        ];
    }

    /**
     * O status "Em uso" é controlado pelas movimentações:
     * com empréstimo em aberto ele não pode sair de "Em uso"; sem empréstimo, não pode ser definido manualmente.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $equipamento = $this->route('equipamento');
                if (! $equipamento instanceof Equipamento || $validator->errors()->has('status')) {
                    return;
                }

                $emprestimo = $equipamento->emprestimoEmAberto();
                $status = $this->input('status');

                if ($emprestimo && $status !== 'em_uso') {
                    $validator->errors()->add('status', "Equipamento em uso pela movimentação #{$emprestimo->movimentacao_id}. Registre a devolução para alterar o status.");
                } elseif (! $emprestimo && $status === 'em_uso') {
                    $validator->errors()->add('status', 'O status "Em uso" é definido automaticamente ao registrar um termo de responsabilidade.');
                }
            },
        ];
    }

    public function attributes(): array
    {
        return [
            'tipo_equipamento_id' => 'tipo de equipamento',
            'data_compra' => 'data da compra',
            'valor_compra' => 'valor da compra',
            'status' => 'status',
            'descricao' => 'descrição',
            'patrimonio' => 'patrimônio',
            'numero_serie' => 'número de série',
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_equipamento_id.required' => 'Selecione um tipo de equipamento.',
            'tipo_equipamento_id.exists' => 'O tipo de equipamento informado não foi encontrado.',
            'status.required' => 'Informe o status do equipamento.',
            'status.in' => 'Status inválido.',
            'valor_compra.numeric' => 'Informe um valor numérico válido.',
            'descricao.max' => 'A descrição pode ter no máximo 65.535 caracteres.',
            'patrimonio.unique' => 'Já existe um equipamento com este patrimônio.',
            'numero_serie.unique' => 'Já existe um equipamento com este número de série.',
        ];
    }
}
