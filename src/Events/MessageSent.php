<?php

namespace SimpleChat\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string $conversationId,
        public readonly array $message,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('simple-chat.' . $this->conversationId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'MessageSent';
    }

    public function broadcastWith(): array
    {
        return $this->message;
    }
}
