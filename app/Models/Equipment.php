<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'acquisition_date',
        'acquisition_value',
        'equipment_type_id',
        'status',
        'active'
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'acquisition_value' => 'double',
        'active' => 'boolean'
    ];

    public function equipmentType()
    {
        return $this->belongsTo(EquipmentType::class);
    }
}
