<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'purchase_date',
        'purchase_value',
        'equipment_type_id',
        'state',
        'active',
        'description',
        'patrimony',
        'serial_number'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_value' => 'decimal:2',
        'active' => 'boolean'
    ];

    public function equipmentType()
    {
        return $this->belongsTo(EquipmentType::class);
    }

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }

    //TODO: Ajustar desse mdoel para frente nos relacionamentos
}
