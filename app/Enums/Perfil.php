<?php

namespace App\Enums;

/**
 * Perfil de acesso do usuário. As permissões de cada perfil ficam nos Gates (AppServiceProvider).
 */
enum Perfil: string
{
    /** Tecnologia da Informação e Comunicação: acesso completo. */
    case TIC = 'tic';

    /** Departamento Pessoal: funcionários (cadastro, edição, pendências e desligamento). */
    case DP = 'dp';

    public function label(): string
    {
        return match ($this) {
            self::TIC => 'TIC',
            self::DP => 'Departamento Pessoal',
        };
    }

    /** ['tic' => 'TIC', 'dp' => 'Departamento Pessoal'] para selects. */
    public static function options(): array
    {
        return array_combine(
            array_map(fn (self $perfil) => $perfil->value, self::cases()),
            array_map(fn (self $perfil) => $perfil->label(), self::cases()),
        );
    }
}
