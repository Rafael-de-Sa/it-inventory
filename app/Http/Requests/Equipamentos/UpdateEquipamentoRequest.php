<?php

namespace App\Http\Requests\Equipamentos;

use App\Models\Equipamento;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateEquipamentoRequest extends EquipamentoRequest
{
    protected function equipamentoId(): ?int
    {
        return $this->route('equipamento')?->id;
    }

    public function rules(): array
    {
        return parent::rules() + [
            'status' => ['required', Rule::in(array_keys(Equipamento::STATUS))],
        ];
    }

    /**
     * O status "Em uso" é controlado pelas movimentações:
     * com empréstimo em aberto ele não pode sair de "Em uso"; sem empréstimo, não pode ser definido manualmente.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $equipamento = $this->route('equipamento');
                if (! $equipamento instanceof Equipamento || $validator->errors()->has('status')) {
                    return;
                }

                $emprestimo = $equipamento->emprestimoEmAberto();
                $status = $this->input('status');

                if ($emprestimo && $status !== 'em_uso') {
                    $validator->errors()->add('status', "Equipamento em uso pela movimentação #{$emprestimo->movimentacao_id}. Registre a devolução para alterar o status.");
                } elseif (! $emprestimo && $status === 'em_uso') {
                    $validator->errors()->add('status', 'O status "Em uso" é definido automaticamente ao registrar um termo de responsabilidade.');
                }
            },
        ];
    }
}
