<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Flow extends Model
{
    use HasFactory, BelongsToWorkspace;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'flow_data' => 'array',
            'is_active' => 'boolean',
            'execution_count' => 'integer',
        ];
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(FlowSession::class);
    }
}
