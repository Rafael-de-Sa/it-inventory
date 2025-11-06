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
            // Busca por ID da movimentação
            'busca' => ['nullable', 'string', 'max:50'],

            // Combos de filtro
            'empresa_id'     => ['nullable', 'integer', 'exists:empresas,id'],
            'setor_id'       => ['nullable', 'integer', 'exists:setores,id'],
            'funcionario_id' => ['nullable', 'integer', 'exists:funcionarios,id'],

            // Status (ajuste os valores conforme seu enum)
            'status' => ['nullable', 'string', 'max:50'],

            // Ordenação
            'ordenar_por'    => ['nullable', 'in:id,data,status'],
            'direcao'        => ['nullable', 'in:asc,desc'],
        ];
    }

    public function messages(): array
    {
        return [
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'string'  => 'O campo :attribute deve ser um texto.',
            'max'     => 'O campo :attribute deve possuir no máximo :max caracteres.',
            'in'      => 'O valor selecionado para :attribute é inválido.',
            'exists'  => 'O :attribute selecionado é inválido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'busca'          => 'ID da movimentação',
            'empresa_id'     => 'empresa',
            'setor_id'       => 'setor',
            'funcionario_id' => 'funcionário',
            'status'         => 'status',
            'ordenar_por'    => 'ordenação',
            'direcao'        => 'direção',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'empresa_id'     => $this->filled('empresa_id') ? (int) $this->empresa_id : null,
            'setor_id'       => $this->filled('setor_id') ? (int) $this->setor_id : null,
            'funcionario_id' => $this->filled('funcionario_id') ? (int) $this->funcionario_id : null,
        ]);
    }
}
