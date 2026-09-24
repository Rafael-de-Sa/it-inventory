<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Chave de acesso da NF-e (44 dígitos) com dígito verificador (módulo 11, pesos 2 a 9 da direita para a esquerda).
 */
class ChaveAcessoNfe implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $chave = (string) $value;

        if (! ctype_digit($chave) || strlen($chave) !== 44) {
            $fail('A chave de acesso deve ter 44 dígitos.');
            return;
        }

        if (self::digitoVerificador(substr($chave, 0, 43)) !== (int) $chave[43]) {
            $fail('Chave de acesso inválida: confira os números digitados.');
        }
    }

    public static function digitoVerificador(string $base43): int
    {
        $soma = 0;
        $peso = 2;

        for ($i = strlen($base43) - 1; $i >= 0; $i--) {
            $soma += (int) $base43[$i] * $peso;
            $peso = $peso === 9 ? 2 : $peso + 1;
        }

        $resto = $soma % 11;

        return $resto < 2 ? 0 : 11 - $resto;
    }

    /** Número da nota (nNF) contido na chave: posições 26 a 34, sem zeros à esquerda. */
    public static function numeroNota(string $chave): string
    {
        return ltrim(substr($chave, 25, 9), '0') ?: '0';
    }
}
