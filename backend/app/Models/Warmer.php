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
        'min_sleep',
        'max_sleep',
        'max_daily',
        'createdAt',
    ];

    protected $casts = [
        'instances' => 'array',
        'is_active' => 'boolean',
        'min_sleep' => 'integer',
        'max_sleep' => 'integer',
        'max_daily' => 'integer',
        'createdAt' => 'datetime',
    ];
}
