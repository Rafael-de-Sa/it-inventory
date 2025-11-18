<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
        return
            [
                'email' => ['required', 'email:rfc', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/'],
                'password' => ['required']
            ];
    }

    public function messages(): array
    {
        return [
            'email.email' => 'Informe um e-mail válido.',
            'email.regex' => 'Informe um e-mail em um formato válido (ex.: usuario@dominio.com).'
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'e-mail'
        ];
    }
}
