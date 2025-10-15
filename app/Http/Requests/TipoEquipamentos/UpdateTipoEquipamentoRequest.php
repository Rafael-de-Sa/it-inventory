<?php

namespace App\Http\Requests\TipoEquipamentos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTipoEquipamentoRequest extends FormRequest
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
            'nome' => is_string($nome) ? (string) str($nome)->squish()->trim() : $nome,
            'ativo' => $this->has('ativo') ? (bool) $this->boolean('ativo') : null,
        ]);
    }

    public function rules(): array
    {

        $parametro = $this->route('tipo_equipamento');
        $tipoEquipamentoId = is_object($parametro) ? $parametro->id : (int) $parametro;

        return [
            'nome' => [
                'bail',
                'required',
                'string',
                'min:3',
                'max:45',
                Rule::unique('tipo_equipamentos', 'nome')->ignore($tipoEquipamentoId)->whereNull('apagado_em'),
            ],
            'ativo' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'required'     => 'O campo :attribute é obrigatório.',
            'string'       => 'O campo :attribute deve ser um texto.',
            'min'          => 'O campo :attribute deve possuir ao menos :min caracteres.',
            'max'          => 'O campo :attribute deve possuir no máximo :max caracteres.',
            'boolean'      => 'O campo :attribute deve ser verdadeiro ou falso.',
            'nome.unique'  => 'Já existe um tipo de equipamento com este nome.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nome'  => 'tipo de equipamento',
            'ativo' => 'status ativo',
        ];
    }
}
