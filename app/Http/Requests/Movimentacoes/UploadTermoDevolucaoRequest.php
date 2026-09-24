<?php

namespace App\Http\Requests\Movimentacoes;

use App\Models\Movimentacao;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UploadTermoDevolucaoRequest extends FormRequest
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
            'arquivo_termo' => [
                'required',
                'file',
                'mimes:pdf',
                'max:10240'
            ],
        ];
    }

    /**
     * O termo só pode ser enviado uma vez, em uma devolução não cancelada.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                /** @var Movimentacao $movimentacao */
                $movimentacao = $this->route('movimentacao');

                $erro = match (true) {
                    $movimentacao->tipo_movimentacao !== Movimentacao::TIPO_DEVOLUCAO
                        => 'Esta movimentação não é uma devolução.',
                    $movimentacao->status === 'cancelada'
                        => 'Não é possível enviar o termo de uma movimentação cancelada.',
                    filled($movimentacao->termo_devolucao)
                        => 'O termo de devolução desta movimentação já foi enviado.',
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
            'arquivo_termo' => 'arquivo do termo de devolução',
        ];
    }
}
