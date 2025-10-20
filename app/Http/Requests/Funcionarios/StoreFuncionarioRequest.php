<?php

namespace App\Http\Requests\Funcionarios;

use App\Rules\CpfValido;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFuncionarioRequest extends FormRequest
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
            'empresa_id'   => ['required', 'integer', 'exists:empresas,id'],
            'setor_id'     => ['required', 'integer', 'exists:setores,id'],
            'nome'         => ['required', 'string', 'min:2', 'max:80'],
            'sobrenome'    => ['required', 'string', 'min:2', 'max:80'],
            'cpf'          => [
                'required',
                'string',
                new CpfValido,
                Rule::unique('funcionarios', 'cpf')->whereNull('apagado_em'),
            ],
            'matricula'    => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('funcionarios', 'matricula')->whereNull('apagado_em'),
            ],
            'telefone'     => ['nullable', 'string', 'min:10', 'max:11'],
            'terceirizado' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string'   => 'O campo :attribute deve ser um texto.',
            'min'      => 'O campo :attribute deve possuir ao menos :min caracteres.',
            'max'      => 'O campo :attribute deve possuir no máximo :max caracteres.',
            'size'     => 'O campo :attribute deve conter exatamente :size caracteres.',
            'integer'  => 'O campo :attribute deve ser um número inteiro.',
            'exists'   => 'O :attribute selecionado é inválido.',

            'cpf.unique'        => 'Já existe um funcionário cadastrado com este CPF.',
            'matricula.unique'  => 'Já existe um funcionário cadastrado com esta matrícula.',
            'telefone.min'      => 'Informe um telefone com DDD (10 ou 11 dígitos).',
            'telefone.max'      => 'Informe um telefone com DDD (10 ou 11 dígitos).',
        ];
    }

    public function attributes(): array
    {
        return [
            'empresa_id'  => 'empresa',
            'setor_id'    => 'setor',
            'nome'        => 'nome',
            'sobrenome'   => 'sobrenome',
            'cpf'         => 'CPF',
            'matricula'   => 'matrícula',
            'telefone'    => 'telefone',
            'terceirizado' => 'terceirizado',
        ];
    }
}
