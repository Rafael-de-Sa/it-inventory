<?php

namespace App\Http\Requests\Usuarios;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends FormRequest
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
        $usuarioId = $this->route('usuario');

        return [
            'email' => [
                'required',
                'string',
                'email:rfc',
                'max:100',
                Rule::unique('usuarios', 'email')->ignore($usuarioId),
                'confirmed',
                'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/',
            ],
            'email_confirmation' => ['required_with:email', 'string', 'email', 'max:100'],

            // Senha OPCIONAL (só valida se preencher)
            'senha' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/',
            ],
            'senha_confirmation' => ['nullable', 'string', 'min:8'],

            'ativo' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Mensagens de erro personalizadas.
     */
    public function messages(): array
    {
        return [
            'senha.confirmed' => 'A confirmação da senha não confere.',
            'senha.regex' => 'A senha deve conter ao menos uma letra maiúscula, um número e um caractere especial.',
            'email.email' => 'Informe um e-mail válido.',
            'email.regex' => 'Informe um e-mail em um formato válido (ex.: usuario@dominio.com).',
            'email.unique' => 'Já existe um usuário cadastrado com este e-mail.',
            'email.confirmed' => 'A confirmação de e-mail não confere.',
        ];
    }

    /**
     * Nomes amigáveis dos atributos.
     */
    public function attributes(): array
    {
        return [
            'email' => 'e-mail',
            'email_confirmation' => 'confirmação de e-mail',
            'senha' => 'senha',
            'senha_confirmation' => 'confirmação de senha',
            'ativo' => 'status',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => is_string($this->email) ? strtolower(trim($this->email)) : $this->email,
        ]);

        if ($this->filled('ativo')) {
            $valorAtivo = $this->ativo;

            if (in_array($valorAtivo, ['1', 1, true, 'true'], true)) {
                $ativoNormalizado = true;
            } elseif (in_array($valorAtivo, ['0', 0, false, 'false'], true)) {
                $ativoNormalizado = false;
            } else {
                $ativoNormalizado = null;
            }

            $this->merge(['ativo' => $ativoNormalizado]);
        }
    }
}
