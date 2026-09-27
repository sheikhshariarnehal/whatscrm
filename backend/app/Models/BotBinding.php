<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BotBinding extends Model
{
    use HasFactory, BelongsToWorkspace;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'origin_metadata' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function flow(): BelongsTo
    {
        return $this->belongsTo(Flow::class);
    }
}
