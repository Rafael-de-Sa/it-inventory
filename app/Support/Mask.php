<?php

namespace App\Support;

class Mask
{
    public static function digits(?string $v, ?int $max = null): string
    {
        $d = preg_replace('/\D+/', '', (string) $v);
        return $max ? substr($d, 0, $max) : $d;
    }

    public static function cpf(?string $v): string
    {
        $d = self::digits($v, 11);
        $n = strlen($d);
        if ($n === 0) return '';

        if ($n <= 3)  return $d;
        if ($n <= 6)  return substr($d, 0, 3) . '.' . substr($d, 3);
        if ($n <= 9)  return substr($d, 0, 3) . '.' . substr($d, 3, 3) . '.' . substr($d, 6);
        return substr($d, 0, 3) . '.' . substr($d, 3, 3) . '.' . substr($d, 6, 3) . '-' . substr($d, 9);
    }

    public static function cnpj(?string $v): string
    {
        $d = self::digits($v, 14);
        $n = strlen($d);
        if ($n === 0) return '';

        if ($n <= 2)  return $d;
        if ($n <= 5)  return substr($d, 0, 2) . '.' . substr($d, 2);
        if ($n <= 8)  return substr($d, 0, 2) . '.' . substr($d, 2, 3) . '.' . substr($d, 5);
        if ($n <= 12) return substr($d, 0, 2) . '.' . substr($d, 2, 3) . '.' . substr($d, 5, 3) . '/' . substr($d, 8);

        return substr($d, 0, 2) . '.' . substr($d, 2, 3) . '.' . substr($d, 5, 3)
            . '/' . substr($d, 8, 4) . '-' . substr($d, 12);
    }

    public static function cep(?string $v): string
    {
        $d = self::digits($v, 8);
        $n = strlen($d);
        if ($n === 0) return '';

        if ($n <= 5) return $d;
        return substr($d, 0, 5) . '-' . substr($d, 5);
    }

    public static function telefone(?string $v): string
    {
        $d = self::digits($v, 11);
        $n = strlen($d);
        if ($n === 0) return '';

        if ($n <= 2) {
            return '(' . $d . ($n === 2 ? ')' : '');
        }

        $ddd  = substr($d, 0, 2);
        $rest = substr($d, 2);
        $nr   = strlen($rest);

        if ($nr <= 6) {
            return "($ddd) " . $rest;
        }

        if ($n <= 10) {
            $p1 = substr($rest, 0, 4);
            $p2 = substr($rest, 4);
            return "($ddd) " . $p1 . ($p2 !== '' ? '-' . $p2 : '');
        }

        $p1 = substr($rest, 0, 1);
        $p2 = substr($rest, 1, 4);
        $p3 = substr($rest, 5);
        $out = "($ddd) " . $p1;
        if ($p2 !== '') $out .= $p2;
        if ($p3 !== '') $out .= '-' . $p3;
        return $out;
    }
}
