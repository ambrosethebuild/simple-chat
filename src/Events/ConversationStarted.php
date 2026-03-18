<?php

namespace SimpleChat\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use SimpleChat\Models\Conversation;

class ConversationStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Conversation $conversation,
        public readonly string $mode,
    ) {}

    public function broadcastOn(): array
    {
        if ($this->mode === 'support') {
            // Notify all support agents watching the support queue
            return [new PrivateChannel('simple-chat.support')];
        }

        // Direct mode: notify each non-creator participant on their personal channel
        $participants = is_array($this->conversation->participants)
            ? $this->conversation->participants
            : json_decode($this->conversation->participants ?? '[]', true);

        // Skip index 0 (the creator who already knows about the conversation)
        $others = array_slice($participants, 1);

        return array_map(
            fn($userId) => new PrivateChannel('simple-chat.user.' . $userId),
            $others
        );
    }

    public function broadcastAs(): string
    {
        return 'ConversationStarted';
    }

    public function broadcastWith(): array
    {
        return [
            'id'           => $this->conversation->id,
            'participants' => $this->conversation->participants,
            'status'       => $this->conversation->status,
            'created_at'   => (string) $this->conversation->created_at,
            'updated_at'   => (string) $this->conversation->updated_at,
        ];
    }
}
