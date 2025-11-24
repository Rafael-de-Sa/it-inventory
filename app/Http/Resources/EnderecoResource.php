<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnderecoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'cep' => (string) data_get($this->resource, 'cep'),
            'rua' => (string) data_get($this->resource, 'rua'),
            'bairro' => (string) data_get($this->resource, 'bairro'),
            'cidade' => (string) data_get($this->resource, 'cidade'),
            'estado' => (string) strtoupper((string) data_get($this->resource, 'estado')),
        ];
    }
}
