<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarmerScript extends Model
{
    protected $table = 'warmer_script';
    public $timestamps = false;

    protected $fillable = [
        'uid',
        'message',
        'createdAt',
    ];

    protected $casts = [
        'createdAt' => 'datetime',
    ];
}
