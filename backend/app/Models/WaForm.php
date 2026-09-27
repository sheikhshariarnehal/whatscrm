<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WaForm extends Model
{
    use HasFactory, BelongsToWorkspace;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'categories' => 'array',
            'fields_schema' => 'array',
        ];
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(WaFormSubmission::class);
    }
}
