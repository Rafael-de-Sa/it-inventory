<?php

namespace Tests;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Os testes apagam e recriam o banco (RefreshDatabase). Se a configuração estiver em cache
     * (php artisan config:cache), o DB_DATABASE do phpunit.xml é ignorado e o banco principal seria usado.
     */
    protected function setUpTraits()
    {
        $banco = config('database.connections.' . config('database.default') . '.database');

        if (! str_ends_with((string) $banco, '_testing')) {
            throw new RuntimeException(
                "Testes abortados: o banco \"{$banco}\" não é de testes. Rode \"php artisan config:clear\" e confira o phpunit.xml."
            );
        }

        return parent::setUpTraits();
    }

    /** Autentica um usuário (criado com funcionário, setor e empresa se não for informado). */
    protected function autenticar(?Usuario $usuario = null): Usuario
    {
        $usuario ??= Usuario::factory()->create();
        $this->actingAs($usuario);

        return $usuario;
    }
}
