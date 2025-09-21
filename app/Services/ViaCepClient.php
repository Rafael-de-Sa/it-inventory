<?php

namespace App\Services;

use App\Exceptions\CepNaoEncontradoException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ViaCepClient
{

    /*
     * @return array{cep:string,rua:string,bairro:string,cidade:string,estado:string}
     * @throws CepNaoEncontradoException|\Throwable
     */
    public function buscar(string $cep): array
    {
        $cep = preg_replace('/\D+/', '', $cep);
        $key = "viacep:{$cep}";

        return Cache::remember($key, now()->addDay(), function () use ($cep) {
            $resp = Http::baseUrl('https://viacep.com.br/ws')
                ->timeout(5)
                ->retry(2, 250)
                ->acceptJson()
                ->get("{$cep}/json/");

            $resp->throw();

            $data = $resp->json();
            if (!is_array($data) || isset($data['erro'])) {
                throw new CepNaoEncontradoException("CEP {$cep} não encontrado.");
            }

            return [
                'cep'    => preg_replace('/\D+/', '', (string)($data['cep'] ?? $cep)),
                'rua'    => (string)($data['logradouro'] ?? ''),
                'bairro' => (string)($data['bairro'] ?? ''),
                'cidade' => (string)($data['localidade'] ?? ''),
                'estado' => strtoupper((string)($data['uf'] ?? '')),
            ];
        });
    }
}
