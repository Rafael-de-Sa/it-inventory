<?php

namespace App\Http\Requests\Funcionarios;

use App\Rules\CpfValido;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFuncionarioRequest extends FormRequest
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
        $somenteDigitos = static fn($v) => $v !== null ? preg_replace('/\D+/', '', (string) $v) : null;

        $this->merge([
            'cpf'         => $somenteDigitos($this->input('cpf')) ?: null,
            'telefone'    => $somenteDigitos($this->input('telefone')) ?: null,
            // checkbox ausente => false (0); presente => true (1)
            'terceirizado' => $this->boolean('terceirizado') ? 1 : 0,
            'ativo'       => $this->boolean('ativo') ? 1 : 0,
            'matricula'   => $this->filled('matricula') ? trim((string) $this->input('matricula')) : null,
        ]);
    }

    public function rules(): array
    {
        $id = $this->route('funcionario') ?? $this->route('id') ?? $this->route('funcionarios');

        return [
            // empresa só dirige o filtro de setores no front; não será persistida
            'empresa_id'   => ['nullable', 'integer', 'exists:empresas,id'],

            'setor_id'     => ['required', 'integer', 'exists:setores,id'],

            'nome'         => ['required', 'string', 'min:2', 'max:80'],
            'sobrenome'    => ['required', 'string', 'min:2', 'max:80'],

            'cpf'          => [
                'required',
                'digits:11',
                new CpfValido(),
                Rule::unique('funcionarios', 'cpf')
                    ->ignore($id, 'id')
                    ->whereNull('apagado_em'),
            ],

            // obrigatório quando NÃO terceirizado (terceirizado != 1)
            'matricula'    => [
                'required_unless:terceirizado,1',
                'string',
                'max:30',
                Rule::unique('funcionarios', 'matricula')
                    ->ignore($id, 'id')
                    ->whereNull('apagado_em'),
            ],

            // telefone com DDD: 10 ou 11 dígitos
            'telefone'     => ['nullable', 'digits_between:10,11'],

            'terceirizado' => ['required', 'boolean'],
            'ativo'        => ['required', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'empresa_id'   => 'empresa',
            'setor_id'     => 'setor',
            'nome'         => 'nome',
            'sobrenome'    => 'sobrenome',
            'cpf'          => 'CPF',
            'matricula'    => 'matrícula',
            'telefone'     => 'telefone',
            'terceirizado' => 'terceirizado',
            'ativo'        => 'ativo',
        ];
    }

    public function messages(): array
    {
        return [
            // genéricas
            '*.required'            => 'O campo :attribute é obrigatório.',
            '*.string'              => 'O campo :attribute deve ser um texto.',
            '*.min'                 => 'O campo :attribute deve possuir ao menos :min caracteres.',
            '*.max'                 => 'O campo :attribute deve possuir no máximo :max caracteres.',
            '*.integer'             => 'O campo :attribute deve ser um número inteiro.',
            '*.boolean'             => 'O campo :attribute deve ser verdadeiro ou falso.',
            '*.exists'              => 'O :attribute selecionado é inválido.',
            '*.unique'              => 'Já existe :attribute com esse valor.',
            'cpf.digits'            => 'O CPF deve conter exatamente 11 dígitos.',
            'telefone.digits_between' => 'Informe um telefone com DDD (10 ou 11 dígitos).',

            // específicas
            'setor_id.required'     => 'Selecione um setor.',
            'nome.required'         => 'Informe o nome.',
            'sobrenome.required'    => 'Informe o sobrenome.',
            'cpf.required'          => 'Informe o CPF.',
            'matricula.required_unless' => 'Informe a matrícula para funcionários próprios (não terceirizados).',
        ];
    }
}
