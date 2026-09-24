<?php

namespace App\Http\Requests\Movimentacoes;

use App\Models\Movimentacao;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UploadTermoResponsabilidadeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'arquivo_termo' => [
                'required',
                'file',
                'mimes:pdf',
                'max:10240'
            ],
        ];
    }

    /**
     * O termo só pode ser enviado uma vez, em um termo de responsabilidade não cancelado.
     * A tela já esconde o envio nesses casos; aqui a regra vale também para requisições diretas.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                /** @var Movimentacao $movimentacao */
                $movimentacao = $this->route('movimentacao');

                $erro = match (true) {
                    $movimentacao->tipo_movimentacao !== Movimentacao::TIPO_RESPONSABILIDADE
                        => 'Esta movimentação não é um termo de responsabilidade.',
                    $movimentacao->status === 'cancelada'
                        => 'Não é possível enviar o termo de uma movimentação cancelada.',
                    filled($movimentacao->termo_responsabilidade)
                        => 'O termo de responsabilidade desta movimentação já foi enviado.',
                    default => null,
                };

                if ($erro) {
                    $validator->errors()->add('arquivo_termo', $erro);
                }
            },
        ];
    }

    public function attributes(): array
    {
        return [
            'arquivo_termo' => 'arquivo do termo de responsabilidade',
        ];
    }
}
