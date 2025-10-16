<?php

namespace App\Http\Requests\Equipamentos;

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
            'campo'         => ['nullable', 'in:,id,tipo,descricao,patrimonio,numero_serie,status'],
            'busca'         => ['nullable', 'string', 'max:255'],
            'ordenar_por'   => ['nullable', 'in:id,tipo,patrimonio,numero_serie,status'],
            'direcao'       => ['nullable', 'in:asc,desc'],
            'status'        => ['nullable', 'in:todos,em_uso,defeituoso,descartado,disponivel,em_manutencao'],
        ];
    }

    public function attributes(): array
    {
        return [
            'campo'       => 'campo',
            'busca'       => 'termo de busca',
            'ordenar_por' => 'ordenação',
            'direcao'     => 'direção',
            'status'      => 'status',
        ];
    }

    public function messages(): array
    {
        return [
            'campo.in'       => 'Campo de filtro inválido.',
            'ordenar_por.in' => 'Campo de ordenação inválido.',
            'direcao.in'     => 'Direção de ordenação inválida.',
            'status.in'      => 'Status inválido.',
        ];
    }
}
