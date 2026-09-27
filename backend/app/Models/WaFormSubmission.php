<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaFormSubmission extends Model
{
    use HasFactory, BelongsToWorkspace;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'submission_data' => 'array',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(WaForm::class, 'wa_form_id');
    }
}
