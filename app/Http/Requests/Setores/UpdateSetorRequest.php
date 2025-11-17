<?php

namespace App\Http\Requests\Setores;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSetorRequest extends FormRequest
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
        $textoOuNulo = fn($valor) => is_string($valor) ? trim($valor) : $valor;

        $this->merge([
            'nome' => $textoOuNulo($this->input('nome')),
            'empresa_id' => $this->filled('empresa_id') ? (int) $this->input('empresa_id') : null,
            'ativo' => $this->boolean('ativo'),
        ]);
    }

    public function rules(): array
    {
        $parametro = $this->route('setor');
        $setorId   = is_object($parametro) ? $parametro->id : (int) $parametro;

        return [
            'nome' => [
                'bail',
                'required',
                'string',
                'min:3',
                'max:50',
                Rule::unique('setores', 'nome')
                    ->ignore($setorId)
                    ->where(fn($q) => $q->where('empresa_id', $this->input('empresa_id'))),
            ],

            'empresa_id' => [
                'required',
                'integer',
                Rule::exists('empresas', 'id')->where(
                    fn($q) =>
                    $q->where('ativo', true)->whereNull('apagado_em')
                ),
            ],

            'ativo' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'empresa_id.exists' => 'A :attribute informada não foi encontrada ou está inativa/arquivada.',
            'nome.unique' => 'Já existe um setor com este nome nesta empresa.',
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
