<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employer_id',
        'trade_name',
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'zipcode',
        'phones',
        'email',
    ];

    protected $casts = [
        'phones' => 'array',
        'active' => 'boolean'
    ];

    public function employer()
    {
        return $this->belongsTo(Employer::class);
    }
}
