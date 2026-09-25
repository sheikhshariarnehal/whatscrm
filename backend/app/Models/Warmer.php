<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warmer extends Model
{
    protected $table = 'warmers';
    public $timestamps = false;

    protected $fillable = [
        'uid',
        'instances',
        'is_active',
        'createdAt',
    ];

    protected $casts = [
        'instances' => 'array',
        'is_active' => 'boolean',
        'createdAt' => 'datetime',
    ];
}
