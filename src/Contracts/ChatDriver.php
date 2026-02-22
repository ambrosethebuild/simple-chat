<?php

namespace SimpleChat\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ChatDriver
{
    /**
     * Initialize or retrieve a conversation.
     *
     * @param string $id Unique identifier for the conversation (e.g., UUID)
     * @param array $participants List of user IDs
     * @return mixed The conversation object or identifier
     */
    public function getConversation(string $id, array $participants = []);

    /**
     * Send a message to a conversation.
     *
     * @param string $conversationId
     * @param string|int $senderId
     * @param string $content
     * @param array $meta Additional metadata
     * @return mixed The created message
     */
    public function sendMessage(string $conversationId, $senderId, string $content, array $meta = []);

    /**
     * Retrieve messages from a conversation.
     *
     * @param string $conversationId
     * @param int $limit
     * @param int $offset
     * @return iterable
     */
    public function getMessages(string $conversationId, int $limit = 50, int $offset = 0);
}
