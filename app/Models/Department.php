<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'name'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
