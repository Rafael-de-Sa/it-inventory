<?php

namespace App\Http\Requests\Movimentacoes;

use App\Models\Equipamento;
use App\Models\MovimentacaoEquipamento;
use App\Models\Ocorrencia;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Termo de troca: o equipamento em uso por um funcionário é substituído por outro disponível.
 */
class StoreTrocaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'transferir_identificacao' => $this->boolean('transferir_identificacao'),
            // Na troca pelo fornecedor o motivo é fixo (o antigo recebe baixa).
            'motivo' => $this->input('tipo_troca') === 'fornecedor' ? null : $this->input('motivo'),
        ]);
    }

    public function rules(): array
    {
        return [
            'equipamento_antigo_id' => ['required', 'integer', Rule::exists('equipamentos', 'id')->whereNull('apagado_em')],
            'equipamento_novo_id' => [
                'required', 'integer', 'different:equipamento_antigo_id',
                Rule::exists('equipamentos', 'id')->whereNull('apagado_em'),
            ],
            'tipo_troca' => ['required', Rule::in(['interna', 'fornecedor'])],
            'motivo' => ['nullable', 'required_if:tipo_troca,interna', Rule::in(MovimentacaoEquipamento::MOTIVOS_SELECIONAVEIS)],
            'transferir_identificacao' => ['boolean'],
            'ocorrencia_id' => ['nullable', 'integer', Rule::exists('ocorrencias', 'id')->whereNull('apagado_em')],
            'observacao' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $antigo = Equipamento::find($this->input('equipamento_antigo_id'));
                $novo = Equipamento::find($this->input('equipamento_novo_id'));

                if (! $antigo->emprestimoEmAberto()) {
                    $validator->errors()->add('equipamento_antigo_id', 'Este equipamento não está em uso por nenhum funcionário. A troca substitui um equipamento em uso.');
                }

                if ($novo->status !== 'disponivel' || ! $novo->ativo) {
                    $validator->errors()->add('equipamento_novo_id', 'O equipamento substituto precisa estar ativo e disponível.');
                }

                if ($this->boolean('transferir_identificacao') && filled($antigo->identificacao) && filled($novo->identificacao)) {
                    $validator->errors()->add('transferir_identificacao', "O substituto já tem a identificação \"{$novo->identificacao}\". Desmarque a transferência ou remova a identificação dele antes.");
                }

                $ocorrencia = $this->filled('ocorrencia_id') ? Ocorrencia::find($this->input('ocorrencia_id')) : null;
                if ($ocorrencia && $ocorrencia->equipamento_id !== $antigo->id) {
                    $validator->errors()->add('ocorrencia_id', 'A ocorrência informada é de outro equipamento.');
                } elseif ($ocorrencia?->troca_movimentacao_id) {
                    $validator->errors()->add('ocorrencia_id', "A ocorrência #{$ocorrencia->id} já foi resolvida pela troca #{$ocorrencia->troca_movimentacao_id}.");
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'equipamento_novo_id.different' => 'O equipamento substituto deve ser diferente do equipamento substituído.',
            'motivo.required_if' => 'Informe a condição do equipamento substituído na troca interna.',
        ];
    }

    public function attributes(): array
    {
        return [
            'equipamento_antigo_id' => 'equipamento substituído',
            'equipamento_novo_id' => 'equipamento substituto',
            'tipo_troca' => 'tipo de troca',
            'motivo' => 'condição do equipamento substituído',
            'transferir_identificacao' => 'transferir identificação',
            'ocorrencia_id' => 'ocorrência',
            'observacao' => 'observação',
        ];
    }
}
