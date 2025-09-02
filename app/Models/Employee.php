<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'department_id',
        'first_name',
        'last_name',
        'cpf',
        'registration',
        'dismissal_date',
        'active'
    ];

    protected $casts = [
        'phones' => 'array',
        'dismissal_date' => 'date',
        'active' => 'boolean'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }
}
