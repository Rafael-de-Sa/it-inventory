<?php

namespace App\Http\Requests\TipoEquipamentos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTipoEquipamentoRequest extends FormRequest
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

        $this->merge([
            'nome' => is_string($nome) ? (string) str($nome)->squish->trim : $nome,
        ]);
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'min:3', 'max:45', Rule::unique('tipo_equipamentos', 'nome')->whereNull('apagado_em')],
            'ativo' => ['nullable', 'boolean']
        ];
    }
    public function messages(): array
    {
        return [
            'nome.required' => 'Informe o :attribute.',
            'nome.string' => 'O :attribute deve ser um texto.',
            'nome.min' => 'O :attribute deve ter ao menos :min caracteres.',
            'nome.max' => 'O :attribute deve ter no máximo :max caracteres.',
            'nome.unique' => 'Já existe um :attribute com esse nome.',
            'ativo.boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nome' => 'tipo de equipamento',
            'ativo' => 'status ativo',
        ];
    }
}
