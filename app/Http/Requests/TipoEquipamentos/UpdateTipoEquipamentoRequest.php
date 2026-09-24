<?php

namespace App\Http\Requests\TipoEquipamentos;

use App\Enums\CategoriaEquipamento;
use App\Models\TipoEquipamento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateTipoEquipamentoRequest extends FormRequest
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
        $nome = $this->input('nome');

        $this->merge([
            'nome' => is_string($nome) ? (string) str($nome)->squish()->trim() : $nome,
            'ativo' => $this->has('ativo') ? (bool) $this->boolean('ativo') : null,
        ]);
    }

    public function rules(): array
    {

        $parametro = $this->route('tipo_equipamento');
        $tipoEquipamentoId = is_object($parametro) ? $parametro->id : (int) $parametro;

        return [
            'nome' => [
                'required',
                'string',
                'min:3',
                'max:45',
                Rule::unique('tipo_equipamentos', 'nome')->ignore($tipoEquipamentoId)->whereNull('apagado_em'),
            ],
            'categoria' => ['required', Rule::enum(CategoriaEquipamento::class)],
            'ativo' => ['required', 'boolean'],
        ];
    }

    /** A categoria define a ficha técnica: não muda enquanto houver equipamentos deste tipo. */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $tipo = $this->route('tipo_equipamento');

                if ($tipo instanceof TipoEquipamento
                    && $this->input('categoria') !== $tipo->categoria->value
                    && $tipo->equipamentos()->withTrashed()->exists()) {
                    $validator->errors()->add(
                        'categoria',
                        'A categoria não pode ser alterada porque já existem equipamentos cadastrados com este tipo.'
                    );
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'nome.unique' => 'Já existe um tipo de equipamento com este nome.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nome' => 'tipo de equipamento',
            'categoria' => 'categoria',
            'ativo' => 'status ativo',
        ];
    }
}
