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
            // Funcionário NÃO é alterado no update,
            // então não validamos funcionario_id aqui.

            // E-mail com confirmação
            'email'              => [
                'required',
                'string',
                'email',
                'max:100',
                Rule::unique('usuarios', 'email')->ignore($usuarioId),
                'confirmed',
            ],
            'email_confirmation' => ['required_with:email', 'string', 'email', 'max:100'],

            // Senha OPCIONAL (só valida se preencher)
            'senha'              => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
                // 1 letra maiúscula, 1 número, 1 caractere especial
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/',
            ],
            'senha_confirmation' => ['nullable', 'string', 'min:8'],

            // Status
            'ativo'              => ['nullable', 'boolean'],
        ];
    }

    /**
     * Mensagens de erro personalizadas.
     */
    public function messages(): array
    {
        return [
            'required'                => 'O campo :attribute é obrigatório.',
            'required_with'           => 'O campo :attribute é obrigatório quando :other está preenchido.',
            'string'                  => 'O campo :attribute deve ser um texto.',
            'email'                   => 'Informe um :attribute válido.',
            'max'                     => 'O campo :attribute deve possuir no máximo :max caracteres.',
            'min'                     => 'O campo :attribute deve possuir no mínimo :min caracteres.',
            'boolean'                 => 'O campo :attribute deve ser verdadeiro ou falso.',
            'unique'                  => 'Já existe um registro utilizando este :attribute.',
            'confirmed'               => 'A confirmação de :attribute não confere.',
            'senha.regex'             => 'A senha deve conter ao menos uma letra maiúscula, um número e um caractere especial.',
        ];
    }

    /**
     * Nomes amigáveis dos atributos.
     */
    public function attributes(): array
    {
        return [
            'email'              => 'e-mail',
            'email_confirmation' => 'confirmação de e-mail',
            'senha'              => 'senha',
            'senha_confirmation' => 'confirmação de senha',
            'ativo'              => 'status',
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
