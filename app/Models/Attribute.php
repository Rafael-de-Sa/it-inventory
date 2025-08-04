<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function equipments()
    {
        return $this->belongsToMany(Equipment::class, 'equipment_attributes')
            ->withPivot('value')
            ->withTimestamps();
    }

    public function equipmentAttributes()
    {
        return $this->hasMany(EquipmentAttribute::class);
    }
}
