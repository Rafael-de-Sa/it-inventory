<?php

namespace App\Http\Requests\Funcionarios;

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
            // ↓ removemos 'sobrenome' das listas
            'campo'        => 'nullable|in:id,nome,empresa_nome,empresa_cnpj,setor_nome,matricula',
            'busca'        => 'nullable|string|max:255',
            'ordenar_por'  => 'nullable|in:id,nome,empresa_nome,empresa_cnpj,setor_nome,matricula',
            'direcao'      => 'nullable|in:asc,desc',
            'ativo'        => 'nullable|in:todos,1,0',
            'terceirizado' => 'nullable|in:todos,1,0',
        ];
    }

    public function attributes(): array
    {
        return [
            'campo'        => 'campo',
            'busca'        => 'busca',
            'ordenar_por'  => 'ordenar por',
            'direcao'      => 'direção',
            'ativo'        => 'ativo',
            'terceirizado' => 'terceirizado',
        ];
    }

    public function messages(): array
    {
        return [
            '*.in'           => 'O campo :attribute deve ser um dos seguintes valores: :values.',
            '*.string'       => 'O campo :attribute deve ser um texto.',
            '*.max.string'   => 'O campo :attribute deve ter no máximo :max caracteres.',
            'campo.in'        => 'Selecione um campo válido para filtrar.',
            'ordenar_por.in'  => 'Selecione um campo válido para ordenação.',
            'direcao.in'      => 'A direção deve ser asc (ascendente) ou desc (descendente).',
            'ativo.in'        => 'Filtro de ativo inválido. Use: todos, 1 (ativos) ou 0 (inativos).',
            'terceirizado.in' => 'Filtro de terceirizado inválido. Use: todos, 1 (terceirizados) ou 0 (próprios).',
        ];
    }
}
