<?php

namespace SimpleChat\Drivers;

use SimpleChat\Contracts\ChatDriver;

class AppwriteDriver implements ChatDriver
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getConversation(string $id, array $participants = [])
    {
        // Placeholder for Appwrite implementation
    }

    public function sendMessage(string $conversationId, $senderId, string $content, array $meta = [])
    {
        // Placeholder for Appwrite implementation
    }

    public function getMessages(string $conversationId, int $limit = 50, int $offset = 0)
    {
        // Placeholder for Appwrite implementation
        return [];
    }
}
