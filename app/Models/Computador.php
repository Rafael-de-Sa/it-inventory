<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Ficha técnica de equipamentos da categoria "computador" (desktop, notebook, servidor).
 * Não guarda senhas (ex.: AnyDesk) nem chaves de ativação.
 */
class Computador extends Model
{
    use HasFactory;

    protected $table = 'computadores';
    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';

    public const TIPOS_MEMORIA = ['DDR', 'DDR2', 'DDR3', 'DDR4', 'DDR5', 'LPDDR4', 'LPDDR5'];

    public const FORMATOS_MEMORIA = ['DIMM', 'SODIMM'];

    public const TIPOS_ARMAZENAMENTO = ['HD', 'SSD SATA', 'SSD NVMe'];

    /** Também usado pelos monitores. */
    public const PORTAS_VIDEO = ['HDMI', 'DisplayPort', 'Mini DisplayPort', 'VGA', 'DVI', 'USB-C'];

    protected $fillable = [
        'sistema_operacional',
        'processador',
        'placa_video',
        'memoria_gb',
        'memoria_tipo',
        'memoria_formato',
        'armazenamento_gb',
        'armazenamento_tipo',
        'mac_ethernet',
        'possui_wifi',
        'mac_wifi',
        'portas_video',
        'outras_portas',
        'anydesk_id',
    ];

    protected $casts = [
        'memoria_gb' => 'integer',
        'armazenamento_gb' => 'integer',
        'possui_wifi' => 'boolean',
        'portas_video' => 'array',
    ];

    public function equipamento()
    {
        return $this->belongsTo(Equipamento::class);
    }
}
