<?php

namespace App\Http\Requests\Usuarios;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
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
            'id' => ['nullable', 'integer', 'min:1'],
            'funcionario' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:100'],
            'ativo' => ['nullable', 'boolean'],

            'busca' => ['nullable', 'string', 'max:100'],

            'campo' => ['nullable', 'in:id,funcionario,email'],

            'ordenar_por' => ['nullable', 'in:id,funcionario,email,ultimo_login,ativo'],
            'direcao' => ['nullable', 'in:asc,desc'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser um texto.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'min.integer' => 'O campo :attribute deve ser no mínimo :min.',
            'max' => 'O campo :attribute deve possuir no máximo :max caracteres.',
            'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',
            'email' => 'Informe um :attribute válido.',
            'in' => 'O valor selecionado para :attribute é inválido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => 'ID',
            'funcionario' => 'nome do funcionário',
            'email' => 'e-mail',
            'ativo' => 'status',
            'busca' => 'termo de busca',
            'campo' => 'campo',
            'ordenar_por' => 'campo de ordenação',
            'direcao' => 'direção de ordenação',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'busca' => is_string($this->busca) ? (string) str($this->busca)->squish()->trim() : $this->busca,
            'funcionario' => is_string($this->funcionario) ? (string) str($this->funcionario)->squish()->trim() : $this->funcionario,
            'email' => is_string($this->email) ? (string) str($this->email)->lower()->trim() : $this->email,
            'campo' => is_string($this->campo) ? (string) str($this->campo)->trim() : $this->campo,
            'ordenar_por' => is_string($this->ordenar_por) ? (string) str($this->ordenar_por)->trim() : $this->ordenar_por,
            'direcao' => is_string($this->direcao) ? strtolower($this->direcao) : $this->direcao,
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
