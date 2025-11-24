<?php

namespace App\Http\Requests\Movimentacoes;

use Illuminate\Foundation\Http\FormRequest;

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

    public function attributes(): array
    {
        return [
            'arquivo_termo' => 'arquivo do termo de responsabilidade',
        ];
    }
}
