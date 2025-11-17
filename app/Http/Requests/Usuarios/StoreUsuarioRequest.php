<?php

namespace App\Http\Requests\Usuarios;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioRequest extends FormRequest
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
            'funcionario_id' => ['required', 'exists:funcionarios,id', 'unique:usuarios,funcionario_id'],
            'email' => [
                'required',
                'email:rfc',
                'max:100',
                'unique:usuarios,email',
                'confirmed',
                'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/',
            ],
            'senha' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'funcionario_id' => 'funcionário',
            'email' => 'e-mail',
            'senha' => 'senha',
            'senha_confirmation' => 'confirmação da senha',
            'email_confirmation' => 'confirmação da e-mail',
        ];
    }

    public function messages(): array
    {
        return [
            'funcionario_id.unique' => 'Este funcionário já possui um usuário vinculado.',
            'senha.confirmed' => 'A confirmação da senha não confere.',
            'email.email' => 'Informe um e-mail válido.',
            'email.regex' => 'Informe um e-mail em um formato válido (ex.: usuario@dominio.com).',
            'email.unique' => 'Já existe um usuário cadastrado com este e-mail.',
            'email.confirmed' => 'A confirmação de e-mail não confere.',
            'senha.regex' => 'A senha deve ter ao menos 8 caracteres, 1 letra maiúscula, 1 número e 1 caractere especial.',
        ];
    }
}
