<?php

namespace App\Http\Requests\Ocorrencias;

use App\Models\Ocorrencia;

/**
 * Edição: corrige os dados sem mudar a situação. Numa ocorrência já resolvida, a data de liberação e a solução
 * podem ser corrigidas, mas não apagadas (para reabrir, use a ação "Reabrir").
 */
class UpdateOcorrenciaRequest extends OcorrenciaRequest
{
    public function rules(): array
    {
        /** @var Ocorrencia $ocorrencia */
        $ocorrencia = $this->route('ocorrencia');

        if ($ocorrencia->estaAberta()) {
            return parent::rules();
        }

        return [
            ...parent::rules(),
            'liberado_em' => ['required', 'date', 'after_or_equal:reportado_em', 'before_or_equal:today'],
            'solucao' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            ...parent::messages(),
            'liberado_em.required' => 'A ocorrência está resolvida: informe a data de liberação (para reabrir, use "Reabrir").',
            'solucao.required' => 'Informe a solução.',
        ];
    }
}
