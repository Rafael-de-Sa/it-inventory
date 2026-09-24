<?php

namespace App\Http\Requests\Equipamentos;

use App\Models\Equipamento;
use Illuminate\Validation\Rule;

class StoreEquipamentoRequest extends EquipamentoRequest
{
    protected function equipamentoId(): ?int
    {
        return null;
    }

    public function rules(): array
    {
        return parent::rules() + [
            // "Em uso" só é atribuído ao registrar um termo de responsabilidade.
            'status' => ['required', Rule::in(Equipamento::STATUS_CADASTRO)],
        ];
    }
}
