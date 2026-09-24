<?php

namespace Tests\Unit\Rules;

use App\Rules\CnpjValido;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CnpjValidoTest extends TestCase
{
    private function falhas(string $cnpj): array
    {
        $falhas = [];
        (new CnpjValido)->validate('cnpj', $cnpj, function (string $mensagem) use (&$falhas) {
            $falhas[] = $mensagem;
        });

        return $falhas;
    }

    public static function validos(): array
    {
        return [
            'só dígitos' => ['11222333000181'],
            'com máscara' => ['11.222.333/0001-81'],
            'dígito verificador 0' => ['11444777000161'],
        ];
    }

    #[DataProvider('validos')]
    public function test_aceita_cnpj_valido(string $cnpj): void
    {
        $this->assertSame([], $this->falhas($cnpj));
    }

    public static function invalidos(): array
    {
        return [
            'primeiro dígito errado' => ['11222333000191'],
            'segundo dígito errado' => ['11222333000182'],
            'dígitos repetidos' => ['00000000000000'],
            'curto' => ['1122233300018'],
            'vazio' => [''],
        ];
    }

    #[DataProvider('invalidos')]
    public function test_rejeita_cnpj_invalido(string $cnpj): void
    {
        $this->assertSame(['Informe um CNPJ válido.'], $this->falhas($cnpj));
    }
}
