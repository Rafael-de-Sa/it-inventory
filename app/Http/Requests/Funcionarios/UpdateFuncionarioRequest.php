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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array< mixed>|string>
     */

    protected function prepareForValidation(): void
    {
        $somenteNumeros = fn($campo) =>
        $this->filled($campo) ? preg_replace('/\D+/', '', (string) $this->input($campo)) : null;

        $this->merge([
            'terceirizado' => $this->boolean('terceirizado') ? 1 : 0,
            'ativo' => $this->boolean('ativo') ? 1 : 0,
            'cpf' => $somenteNumeros('cpf'),
            'matricula' => $somenteNumeros('matricula'),
            'telefone' => $somenteNumeros('telefone'),
        ]);
    }

    public function rules(): array
    {
        $funcionario = $this->route('funcionario');
        $funcionarioId = is_object($funcionario) ? $funcionario->id : $funcionario;

        return [
            'empresa_id' => ['required', 'exists:empresas,id'],
            'setor_id' => ['required', 'exists:setores,id'],
            'nome' => ['required', 'string', 'max:255'],
            'sobrenome' => ['required', 'string', 'max:255'],

            'cpf' => [
                'required',
                new CpfValido(),
                Rule::unique('funcionarios', 'cpf')->ignore($funcionarioId),
            ],

            'matricula' => [
                'nullable',
                'string',
                'max:8',
                'required_unless:terceirizado,1',
                Rule::unique('funcionarios', 'matricula')->whereNull('apagado_em')->ignore($funcionarioId),
            ],

            'telefone' => ['nullable', 'string', 'max:15'],
            'terceirizado' => ['nullable', 'boolean'],
            'ativo' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'empresa_id.required' => 'Selecione a empresa.',
            'empresa_id.exists' => 'A empresa selecionada não existe.',
            'setor_id.required' => 'Selecione o setor.',
            'setor_id.exists' => 'O setor selecionado não existe.',
            'nome.required' => 'Informe o nome.',
            'nome.max' => 'O nome pode ter no máximo :max caracteres.',
            'sobrenome.required' => 'Informe o sobrenome.',
            'sobrenome.max' => 'O sobrenome pode ter no máximo :max caracteres.',
            'cpf.required' => 'Informe o CPF.',
            'cpf.unique' => 'Já existe um funcionário com este CPF.',
            'matricula.required_unless' => 'Informe a matrícula para funcionários próprios (não terceirizados).',
            'matricula.numeric' => 'A matrícula deve conter apenas números.',
            'matricula.unique' => 'Já existe um funcionário com esta matrícula.',
            'telefone.max' => 'O telefone pode ter no máximo :max caracteres.',
            'terceirizado.boolean' => 'O campo terceirizado deve ser verdadeiro ou falso.',
            'ativo.boolean' => 'O campo ativo deve ser verdadeiro ou falso.',
        ];
    }
}
