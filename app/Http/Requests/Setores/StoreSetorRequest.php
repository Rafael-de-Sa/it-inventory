<?php

namespace App\Http\Requests\Setores;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSetorRequest extends FormRequest
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
        $this->merge([
            'nome' => is_string($this->input('nome')) ? trim($this->input('nome')) : $this->input('nome'),
            'empresa_id' => $this->input('empresa_id') !== null ? (int) $this->input('empresa_id') : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'min:3', 'max:50'],
            'empresa_id' => [
                'required',
                'integer',
                Rule::exists('empresas', 'id')
                    ->where(
                        fn($q) => $q
                            ->where('ativo', true)
                            ->whereNull('apagado_em')
                    ),
            ],
            'ativo' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string'   => 'O campo :attribute deve ser um texto.',
            'min'      => 'O campo :attribute deve possuir ao menos :min caracteres.',
            'max'      => 'O campo :attribute deve possuir no máximo :max caracteres.',
            'integer'  => 'O campo :attribute deve ser um número inteiro.',
            'exists'   => 'A :attribute informada não foi encontrada.',
            'boolean'  => 'O campo :attribute deve ser verdadeiro ou falso.',
            'empresa_id.exists'   => 'A :attribute informada não foi encontrada ou está inativa/arquivada.'
        ];
    }

    public function attributes(): array
    {
        return [
            'nome' => 'nome do setor',
            'empresa_id' => 'empresa',
            'ativo' => 'status',
        ];
    }
}
