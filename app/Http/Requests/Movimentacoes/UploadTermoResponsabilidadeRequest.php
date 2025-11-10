<?php

namespace App\Http\Requests\Movimentacoes;

use Illuminate\Foundation\Http\FormRequest;

class UploadTermoResponsabilidadeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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

    public function attributes(): array
    {
        return [
            'arquivo_termo' => 'arquivo do termo de responsabilidade',
        ];
    }
}
