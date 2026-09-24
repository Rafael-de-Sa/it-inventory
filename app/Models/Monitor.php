<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Ficha técnica de equipamentos da categoria "monitor". */
class Monitor extends Model
{
    use HasFactory;

    protected $table = 'monitores';
    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';

    public const TIPOS_TELA = ['LED', 'LCD', 'IPS', 'VA', 'TN', 'OLED'];

    protected $fillable = [
        'polegadas',
        'tipo_tela',
        'portas_video',
    ];

    protected $casts = [
        'polegadas' => 'decimal:1',
        'portas_video' => 'array',
    ];

    public function equipamento()
    {
        return $this->belongsTo(Equipamento::class);
    }
}
