<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmpresaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    //true = não valida autorização
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
            'nome_fantasia' => ['required', 'string', 'min:3', 'max:100'],
            'razao_social'  => ['required', 'string', 'min:3', 'max:100'],

            'cnpj'          => ['required', 'string', 'size:14', 'regex:/^\d{14}$/'],
            'cep'           => ['required', 'string', 'size:8', 'regex:/^\d{8}$/'],

            'rua'           => ['required', 'string', 'min:3', 'max:100'],
            'numero'        => ['required', 'string', 'max:8'],
            'complemento'   => ['nullable', 'string', 'max:50'],
            'bairro'        => ['required', 'string', 'min:3', 'max:50'],
            'cidade'        => ['required', 'string', 'min:3', 'max:30'],
            'estado'        => ['required', 'string', 'size:2', 'alpha'],
            'email'         => ['required', 'string', 'email', 'max:60'],
            'telefone' => ['nullable', 'regex:/^\d{10,11}$/']
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
            'alpha'    => 'O campo :attribute deve conter apenas letras.',
            'email'    => 'Informe um :attribute válido.',

            'cnpj.regex' => 'O CNPJ deve conter apenas dígitos (somente números), com 14 caracteres.',
            'cep.regex'  => 'O CEP deve conter apenas dígitos (somente números), com 8 caracteres.',
            'telefone.regex' => 'Informe um telefone válido com DDD (10 ou 11 dígitos).'
        ];
    }

    public function attributes(): array
    {
        return [
            'nome_fantasia' => 'nome fantasia',
            'razao_social'  => 'razão social',
            'cnpj'          => 'CNPJ',
            'rua'           => 'rua',
            'numero'        => 'número',
            'complemento'   => 'complemento',
            'bairro'        => 'bairro',
            'cidade'        => 'cidade',
            'estado'        => 'UF',
            'cep'           => 'CEP',
            'email'         => 'e-mail',
            'telefone'     => 'telefone',
        ];
    }
}
