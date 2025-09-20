<?php

namespace App\Http\Controllers;

use App\Exceptions\CepNaoEncontradoException;
use App\Http\Resources\EnderecoResource;
use App\Services\ViaCepClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ViaCepController extends Controller
{
    public function show(string $cep, ViaCepClient $viaCep): JsonResponse
    {
        // Normaliza e valida (8 dígitos)
        $cep = preg_replace('/\D+/', '', (string) $cep);

        $validator = Validator::make(
            ['cep' => $cep],
            ['cep' => ['required', 'digits:8']],
            ['cep.digits' => 'Informe um CEP com 8 dígitos.']
        );

        if ($validator->fails()) {
            return response()->json([
                'message' => 'CEP inválido.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $endereco = $viaCep->buscar($cep);
            return response()->json(EnderecoResource::make($endereco)->resolve(), 200);
        } catch (CepNaoEncontradoException $e) {
            return response()->json(['message' => 'CEP não encontrado.'], 404);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => 'Falha ao consultar serviço de CEP.'], 502);
        }
    }
}
