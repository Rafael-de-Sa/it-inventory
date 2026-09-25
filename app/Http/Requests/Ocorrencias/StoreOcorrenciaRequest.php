<?php

namespace App\Http\Requests\Ocorrencias;

use Illuminate\Validation\Rule;

class StoreOcorrenciaRequest extends OcorrenciaRequest
{
    public function rules(): array
    {
        return [
            // Equipamentos baixados ou descartados saíram do parque.
            'equipamento_id' => [
                'required', 'integer',
                Rule::exists('equipamentos', 'id')->whereNull('apagado_em')->whereNotIn('status', ['baixado', 'descartado']),
            ],
            'recolher' => ['boolean'],
            ...parent::rules(),
        ];
    }

    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();

        $this->merge(['recolher' => $this->boolean('recolher')]);
    }

    public function messages(): array
    {
        return [
            'equipamento_id.exists' => 'Selecione um equipamento do parque (baixados e descartados não recebem ocorrências).',
            ...parent::messages(),
        ];
    }
}
