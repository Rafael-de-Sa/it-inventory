<?php

namespace App\Http\Requests\Movimentacoes;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
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
        return [
            'busca' => ['nullable', 'string', 'max:50'],

            'empresa_id' => ['nullable', 'integer', 'exists:empresas,id'],
            'setor_id' => ['nullable', 'integer', 'exists:setores,id'],
            'funcionario_id' => ['nullable', 'integer', 'exists:funcionarios,id'],

            'status' => ['nullable', 'string', 'in:pendente,cancelada,concluida,encerrada'],

            'ordenar_por' => ['nullable', 'in:id,data,status'],
            'direcao' => ['nullable', 'in:asc,desc'],
        ];
    }

    public function messages(): array
    {
        return [];
    }

    public function attributes(): array
    {
        return [
            'busca' => 'ID da movimentação',
            'empresa_id' => 'empresa',
            'setor_id' => 'setor',
            'funcionario_id' => 'funcionário',
            'status' => 'status',
            'ordenar_por' => 'ordenação',
            'direcao' => 'direção',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'empresa_id' => $this->filled('empresa_id') ? (int) $this->empresa_id : null,
            'setor_id' => $this->filled('setor_id') ? (int) $this->setor_id : null,
            'funcionario_id' => $this->filled('funcionario_id') ? (int) $this->funcionario_id : null,
        ]);
    }
}
