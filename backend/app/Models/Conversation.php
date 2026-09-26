<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory, BelongsToWorkspace;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
            'last_inbound_at' => 'datetime',
            'expected_close_at' => 'date',
            'unread_count' => 'integer',
            'kanban_order' => 'integer',
            'deal_value' => 'decimal:2',
        ];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function assignedMember(): BelongsTo
    {
        return $this->belongsTo(WorkspaceMember::class, 'assigned_member_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'conversation_tags');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ConversationNote::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function getAvatarUrlAttribute(): string
    {
        // 1. Use the real WhatsApp profile picture synced from the API/webhook
        if (!empty($this->profile_url)) {
            return $this->profile_url;
        }

        // 2. Fall back to the linked CRM contact's avatar if available
        if ($this->contact && !empty($this->contact->avatar_url)) {
            return $this->contact->avatar_url;
        }

        // 3. Generate a deterministic avatar image from the contact name/number
        //    Uses ui-avatars.com — free, no API key, returns a real PNG image
        $seed = $this->sender_name ?? $this->sender_mobile ?? 'WA';
        $name = urlencode($seed);
        return "https://ui-avatars.com/api/?name={$name}&background=00a884&color=ffffff&size=200&bold=true&rounded=true&format=png";
    }
}
