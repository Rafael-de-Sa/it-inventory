<?php

namespace App\Http\Requests\Equipamentos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEquipamentoRequest extends FormRequest
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

    protected function prepareForValidation(): void
    {
        $valorCompra = $this->input('valor_compra');

        if (is_string($valorCompra)) {
            $normalizado = str_replace(['.', ','], ['', '.'], $valorCompra);
        } else {
            $normalizado = $valorCompra;
        }

        $ativo = $this->boolean('ativo');

        $this->merge([
            'valor_compra' => $normalizado !== '' ? $normalizado : null,
            'ativo' => $ativo,
            'status' => $this->input('status') ?: 'disponivel',
            'patrimonio' => $this->input('patrimonio') ? (string) str($this->input('patrimonio'))->squish()->trim() : null,
            'numero_serie' => $this->input('numero_serie') ? (string) str($this->input('numero_serie'))->squish()->trim() : null,
            'descricao' => $this->input('descricao') ? (string) str($this->input('descricao'))->trim() : null,
            'tipo_equipamento_id' => $this->filled('tipo_equipamento_id') ? (int) $this->input('tipo_equipamento_id') : null,
        ]);
    }


    public function rules(): array
    {
        return [
            'tipo_equipamento_id' => ['required', 'integer', 'exists:tipo_equipamentos,id'],
            'data_compra' => ['nullable', 'date'],
            'valor_compra' => ['nullable', 'numeric', 'between:0,9999999999.99'],
            'status' => ['required', 'in:em_uso,defeituoso,descartado,disponivel,em_manutencao'],
            'descricao' => ['required', 'string', 'max:65535'],
            'patrimonio' => ['nullable', 'string', 'max:255', 'unique:equipamentos,patrimonio'],
            'numero_serie' => ['nullable', 'string', 'max:255', 'unique:equipamentos,numero_serie'],
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_equipamento_id.required' => 'Selecione um tipo de equipamento.',
            'tipo_equipamento_id.exists' => 'O tipo de equipamento informado não foi encontrado.',
            'status.required' => 'Informe o status do equipamento.',
            'status.in' => 'Status inválido.',
            'valor_compra.numeric' => 'Informe um valor numérico válido.',
            'descricao.max' => 'A descrição pode ter no máximo 65.535 caracteres.',
            'patrimonio.unique' => 'Já existe um equipamento com este patrimônio.',
            'numero_serie.unique' => 'Já existe um equipamento com este número de série.',
        ];
    }

    public function attributes(): array
    {
        return [
            'tipo_equipamento_id' => 'tipo de equipamento',
            'status' => 'status',
            'patrimonio' => 'patrimônio',
            'numero_serie' => 'número de série',
            'data_compra' => 'data da compra',
            'valor_compra' => 'valor da compra',
            'descricao' => 'descrição',
            'ativo' => 'ativo',
        ];
    }
}
