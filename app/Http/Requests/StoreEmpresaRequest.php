<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CnpjValido;
use App\Enums\Uf;
use Illuminate\Validation\Rule;

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

    protected function prepareForValidation(): void
    {
        $onlyDigits = fn($v) => is_string($v) ? preg_replace('/\D+/', '', $v) : $v;


        $this->merge([
            'cnpj'     => $onlyDigits($this->input('cnpj')),
            'cep'      => $onlyDigits($this->input('cep')),
            'telefone' => $onlyDigits($this->input('telefone')),
            'estado'   => strtoupper((string) $this->input('estado'))
        ]);
    }


    public function rules(): array
    {
        return [
            'nome_fantasia' => ['required', 'string', 'min:3', 'max:100'],
            'razao_social'  => ['required', 'string', 'min:3', 'max:100'],

            'cnpj' => ['required', 'digits:14', new CnpjValido],
            'cep'  => ['required', 'digits:8'],

            'rua'           => ['required', 'string', 'min:3', 'max:100'],
            'numero'        => ['required', 'string', 'max:8'],
            'complemento'   => ['nullable', 'string', 'max:50'],
            'bairro'        => ['required', 'string', 'min:3', 'max:50'],
            'cidade'        => ['required', 'string', 'min:3', 'max:30'],
            'estado'        => ['required', 'string', 'size:2', 'alpha', Rule::in(Uf::values())],
            'email'         => ['required', 'string', 'email', 'max:60'],
            'telefone' => ['nullable', 'digits_between:10,11']
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

            'cnpj.digits'           => 'O CNPJ deve conter exatamente 14 dígitos.',
            'cep.digits'            => 'O CEP deve conter exatamente 8 dígitos.',
            'telefone.digits_between' => 'Informe um telefone com DDD (10 ou 11 dígitos).',
            'estado.size' => 'UF deve ter 2 letras.',
            'estado.in' => 'UF inválida.',
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
