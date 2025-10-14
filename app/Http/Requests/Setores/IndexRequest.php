<?php

namespace App\Http\Requests\Setores;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'nome'        => ['nullable', 'string', 'max:100'],
            'empresa_id'  => ['nullable', 'integer', 'min:1', Rule::exists('empresas', 'id')],
            'ativo'       => ['nullable', 'in:0,1'],

            'ordenar_por' => ['nullable', 'in:id,nome,empresa_id,nome_empresa,cnpj_empresa,ativo'],
            'direcao'     => ['nullable', 'in:asc,desc'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('ativo')) {
            $this->merge([
                'ativo' => in_array($this->ativo, ['0', '1'], true) ? $this->ativo : null,
            ]);
        }
    }
}
