<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CnpjValido implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $d = preg_replace('/\D+/', '', (string) $value);

        if (strlen($d) !== 14 || preg_match('/^(\d)\1{13}$/', $d)) {
            $fail('Informe um CNPJ válido.');
            return;
        }

        $nums = array_map('intval', str_split($d));
        $dv1  = $this->calc(array_slice($nums, 0, 12), [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]);
        if ($dv1 !== $nums[12]) {
            $fail('Informe um CNPJ válido.');
            return;
        }

        $dv2  = $this->calc(array_slice($nums, 0, 13), [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]);
        if ($dv2 !== $nums[13]) {
            $fail('Informe um CNPJ válido.');
        }
    }
    private function calc(array $base, array $pesos): int
    {
        $soma = 0;
        foreach ($base as $i => $n) $soma += $n * $pesos[$i];
        $r = $soma % 11;
        return $r < 2 ? 0 : 11 - $r;
    }
}
