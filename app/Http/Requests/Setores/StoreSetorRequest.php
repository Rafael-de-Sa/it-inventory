<?php

namespace App\Http\Requests\Setores;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Symfony\Component\Console\Input\Input;

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
        $nome = $this->input('nome');
        $empresaId = $this->input('empresa_id');

        $this->merge([
            'nome' => is_string($nome) ? (string) str($nome)->squish()->trim() : $nome,
            'empresa_id' => filled($empresaId) ? (int) $empresaId : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nome' => [
                'required',
                'string',
                'min:3',
                'max:50',
                Rule::unique('setores', 'nome')
                    ->where(fn($q) => $q->where('empresa_id', $this->input('empresa_id'))),
            ],
            'empresa_id' => [
                'bail',
                'required',
                'integer',
                Rule::exists('empresas', 'id')
                    ->where(fn($q) => $q->where('ativo', true)->whereNull('apagado_em')),
            ],
        ];
    }


    public function messages(): array
    {
        return [
            'required'           => 'O campo :attribute é obrigatório.',
            'string'             => 'O campo :attribute deve ser um texto.',
            'min'                => 'O campo :attribute deve possuir ao menos :min caracteres.',
            'max'                => 'O campo :attribute deve possuir no máximo :max caracteres.',
            'integer'            => 'O campo :attribute deve ser um número inteiro.',
            'empresa_id.exists'  => 'A :attribute informada não foi encontrada ou está inativa/arquivada.',
            'nome.unique'        => 'Já existe um setor com este nome nesta empresa.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nome'       => 'nome do setor',
            'empresa_id' => 'empresa',
        ];
    }
}
