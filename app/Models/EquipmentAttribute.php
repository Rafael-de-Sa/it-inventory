<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquipmentAttribute extends Model
{
    use HasFactory, SoftDeletes;
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}
