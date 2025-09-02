<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employer extends Model
{
    use HasFactory, SoftDeletes;

    // Campos que podem ser preenchidos via create/update
    protected $fillable = [
        'trade_name',
        'corporate_name',
        'cnpj',
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'zipcode',
        'website',
        'email',
        'active',
        'phones',
        'logo_path'
    ];

    // Casts para tipos específicos
    protected $casts = [
        'phones' => 'array',
        'active' => 'boolean'
    ];

    public function departments()
    {
        return $this->hasMany(Department::class);
    }
}
