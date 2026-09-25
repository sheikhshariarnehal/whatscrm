<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('workspace.{workspaceId}', function (User $user, int $workspaceId) {
    return $user->workspaceMemberships()
        ->where('workspace_id', $workspaceId)
        ->where('is_active', true)
        ->exists();
});

Broadcast::channel('conversation.{conversationId}', function (User $user, int $conversationId) {
    $conversation = \App\Models\Conversation::withoutGlobalScope(\App\Scopes\WorkspaceScope::class)->find($conversationId);
    if (! $conversation) {
        return false;
    }

    return $user->workspaceMemberships()
        ->where('workspace_id', $conversation->workspace_id)
        ->where('is_active', true)
        ->exists();
});
