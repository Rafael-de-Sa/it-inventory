<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employer_id',
        'name',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function employer()
    {
        return $this->belongsTo(Employer::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }
}
