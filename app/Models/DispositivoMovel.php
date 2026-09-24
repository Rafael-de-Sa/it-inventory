<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Ficha técnica de equipamentos da categoria "dispositivo móvel" (celulares e maquininhas Smart/POS).
 * Chip e linha não são cadastrados.
 */
class DispositivoMovel extends Model
{
    use HasFactory;

    protected $table = 'dispositivos_moveis';
    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';

    protected $fillable = [
        'imei_1',
        'imei_2',
        'mac',
    ];

    public function equipamento()
    {
        return $this->belongsTo(Equipamento::class);
    }
}
