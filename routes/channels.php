<?php

use Illuminate\Support\Facades\Broadcast;
use SimpleChat\Models\Conversation;

/*
|--------------------------------------------------------------------------
| Simple Chat Broadcast Channels
|--------------------------------------------------------------------------
|
| Three private channels are registered:
|
|   simple-chat.{conversationId}
|       Scoped to a single conversation. Authorized for participants only.
|       Used by: MessageSent event.
|
|   simple-chat.support
|       General support queue channel. Authorized for users with the
|       configured view_tickets permission.
|       Used by: ConversationStarted event (support mode).
|
|   simple-chat.user.{userId}
|       Per-user personal channel. Users may only subscribe to their own.
|       Used by: ConversationStarted event (direct mode).
|
| You may publish this file for customization:
|   php artisan vendor:publish --tag=simple-chat-channels
|
*/

Broadcast::channel('simple-chat.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::withTrashed()->find($conversationId);

    if (!$conversation) {
        return false;
    }

    $participants = is_array($conversation->participants)
        ? $conversation->participants
        : json_decode($conversation->participants ?? '[]', true);

    return in_array((string) $user->id, array_map('strval', $participants ?: []));
});

Broadcast::channel('simple-chat.support', function ($user) {
    $viewPerm = config('simple-chat.support.permissions.view_tickets', 'view-tickets');
    return $user->can($viewPerm);
});

Broadcast::channel('simple-chat.user.{userId}', function ($user, $userId) {
    return (string) $user->id === (string) $userId;
});
