<?php

namespace App\Enums;

use App\Models\Computador;
use App\Models\DispositivoMovel;
use App\Models\Impressora;
use App\Models\Monitor;

/**
 * Categoria do tipo de equipamento: define qual tabela guarda a ficha técnica
 * (computadores, monitores, impressoras ou dispositivos_moveis). "Genérico" usa só os campos comuns.
 */
enum CategoriaEquipamento: string
{
    case COMPUTADOR = 'computador';
    case MONITOR = 'monitor';
    case IMPRESSORA = 'impressora';
    case DISPOSITIVO_MOVEL = 'dispositivo_movel';
    case GENERICO = 'generico';

    public function label(): string
    {
        return match ($this) {
            self::COMPUTADOR => 'Computador',
            self::MONITOR => 'Monitor',
            self::IMPRESSORA => 'Impressora',
            self::DISPOSITIVO_MOVEL => 'Dispositivo móvel',
            self::GENERICO => 'Genérico',
        };
    }

    public function descricao(): string
    {
        return match ($this) {
            self::COMPUTADOR => 'Desktop, notebook, servidor: sistema, processador, memória, armazenamento e rede.',
            self::MONITOR => 'Tamanho, tipo de tela e portas.',
            self::IMPRESSORA => 'Tecnologia de impressão, conexões e MAC.',
            self::DISPOSITIVO_MOVEL => 'Celulares e maquininhas: IMEI e MAC.',
            self::GENERICO => 'Somente os campos comuns (ex.: PINPad, teclado, headset).',
        };
    }

    /** Model da ficha técnica, ou null para "Genérico". */
    public function modelEspecificacao(): ?string
    {
        return match ($this) {
            self::COMPUTADOR => Computador::class,
            self::MONITOR => Monitor::class,
            self::IMPRESSORA => Impressora::class,
            self::DISPOSITIVO_MOVEL => DispositivoMovel::class,
            self::GENERICO => null,
        };
    }

    /** Nome da relação hasOne em Equipamento, ou null para "Genérico". */
    public function relacao(): ?string
    {
        return match ($this) {
            self::COMPUTADOR => 'computador',
            self::MONITOR => 'monitor',
            self::IMPRESSORA => 'impressora',
            self::DISPOSITIVO_MOVEL => 'dispositivoMovel',
            self::GENERICO => null,
        };
    }

    public static function options(): array
    {
        return array_combine(
            array_map(fn (self $categoria) => $categoria->value, self::cases()),
            array_map(fn (self $categoria) => $categoria->label(), self::cases()),
        );
    }
}
