<?php

namespace App\Support;

class Mask
{
    /** Mantém só dígitos (opcionalmente limita tamanho). */
    public static function digits(?string $v, ?int $max = null): string
    {
        $d = preg_replace('/\D+/', '', (string) $v);
        return $max ? substr($d, 0, $max) : $d;
    }

    /** CNPJ (parcial ou completo)
     *  2 -> 12 -> 14 dígitos  =>  00 . 000 . 000 / 0000 - 00
     */
    public static function cnpj(?string $v): string
    {
        $d = self::digits($v, 14);
        $n = strlen($d);
        if ($n === 0) return '';

        if ($n <= 2)  return $d;
        if ($n <= 5)  return substr($d, 0, 2) . '.' . substr($d, 2);
        if ($n <= 8)  return substr($d, 0, 2) . '.' . substr($d, 2, 3) . '.' . substr($d, 5);
        if ($n <= 12) return substr($d, 0, 2) . '.' . substr($d, 2, 3) . '.' . substr($d, 5, 3) . '/' . substr($d, 8);

        // 13-14
        return substr($d, 0, 2) . '.' . substr($d, 2, 3) . '.' . substr($d, 5, 3)
            . '/' . substr($d, 8, 4) . '-' . substr($d, 12);
    }

    /** CEP (parcial ou completo)
     *  1..5 => 00000
     *  6..8 => 00000-0..  / 00000-000
     */
    public static function cep(?string $v): string
    {
        $d = self::digits($v, 8);
        $n = strlen($d);
        if ($n === 0) return '';

        if ($n <= 5) return $d;
        return substr($d, 0, 5) . '-' . substr($d, 5);
    }

    /** Telefone único (parcial ou completo)
     *  Até 10 dígitos => (AA) NNNN-NNNN (progressivo)
     *  11 dígitos     => (AA) 9NNNN-NNNN
     */
    public static function telefone(?string $v): string
    {
        $d = self::digits($v, 11);
        $n = strlen($d);
        if ($n === 0) return '';

        // DDD parcial
        if ($n <= 2) {
            // exibe já com "(" e fecha quando tiver 2 dígitos
            return '(' . $d . ($n === 2 ? ')' : '');
        }

        $ddd  = substr($d, 0, 2);
        $rest = substr($d, 2);
        $nr   = strlen($rest);

        // Até 6 dígitos após DDD (ainda sem hífen)
        if ($nr <= 6) {
            return "($ddd) " . $rest;
        }

        // Entre 7 e 10 dígitos após DDD: assume padrão de 10 dígitos (fixo)
        if ($n <= 10) {
            // (AA) NNNN-NNNN (parcial se ainda não completou)
            $p1 = substr($rest, 0, 4);
            $p2 = substr($rest, 4);
            return "($ddd) " . $p1 . ($p2 !== '' ? '-' . $p2 : '');
        }

        // 11 dígitos (celular): (AA) 9NNNN-NNNN
        $p1 = substr($rest, 0, 1);   // 9
        $p2 = substr($rest, 1, 4);   // NNNN
        $p3 = substr($rest, 5);      // NNNN (talvez parcial)
        $out = "($ddd) " . $p1;
        if ($p2 !== '') $out .= $p2;
        if ($p3 !== '') $out .= '-' . $p3;
        return $out;
    }
}
