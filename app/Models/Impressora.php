<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Ficha técnica de equipamentos da categoria "impressora" (A4, BP-e, etiqueta...). */
class Impressora extends Model
{
    use HasFactory;

    protected $table = 'impressoras';
    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';

    public const TECNOLOGIAS = [
        'tanque_tinta' => 'Jato de tinta / tanque',
        'laser' => 'Laser',
        'termica' => 'Térmica',
        'matricial' => 'Matricial',
    ];

    public const CONEXOES = [
        'usb' => 'USB',
        'rede' => 'Rede (cabo)',
        'wifi' => 'Wi-Fi',
        'bluetooth' => 'Bluetooth',
    ];

    protected $fillable = [
        'tecnologia',
        'conexoes',
        'mac',
    ];

    protected $casts = [
        'conexoes' => 'array',
    ];

    public function equipamento()
    {
        return $this->belongsTo(Equipamento::class);
    }
}
