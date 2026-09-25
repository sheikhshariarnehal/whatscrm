<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instance extends Model
{
    protected $table = 'instance';
    public $timestamps = false;

    protected $fillable = [
        'uid',
        'title',
        'number',
        'uniqueId',
        'qr',
        'data',
        'other',
        'status',
        'createdAt',
    ];

    protected $casts = [
        'data' => 'array',
        'other' => 'array',
        'createdAt' => 'datetime',
    ];

    public function isActive(): bool
    {
        return in_array(strtoupper($this->status ?? ''), ['ACTIVE', 'CONNECTED']);
    }
}
