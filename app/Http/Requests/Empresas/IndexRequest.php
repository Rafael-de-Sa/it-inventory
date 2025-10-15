<?php

namespace App\Http\Requests\Empresas;

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
            'nome_fantasia' => ['nullable', 'string', 'max:255'],
            'razao_social' => ['nullable', 'string', 'max:255'],
            'cnpj' => ['nullable', 'string', 'max:18'],
            'email' => ['nullable', 'string', 'max:255', 'email'],
            'cidade' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'string', 'size:2'],
            'ativo'  => ['nullable', 'boolean'],


            'busca' => ['nullable', 'string', 'max:100'],
            'campo' => ['nullable', 'in:id,nome_fantasia,razao_social,cnpj,cidade,estado'],
            'ordenar_por' => ['nullable', 'in:id,nome_fantasia,razao_social,cnpj,cidade,estado,ativo'],
            'direcao' => ['nullable', 'in:asc,desc']
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('cnpj')) {
            $this->merge([
                'cnpj' => preg_replace('/\D+/', '', (string) $this->cnpj),
            ]);
        }

        if ($this->filled('estado') && is_string($this->estado)) {
            $this->merge(['estado' => strtoupper($this->estado)]);
        }
    }
}
