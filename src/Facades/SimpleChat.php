<?php

namespace SimpleChat\Facades;

use Illuminate\Support\Facades\Facade;
use SimpleChat\Contracts\ChatDriver;

/**
 * @method static mixed getConversation(string $id, array $participants = [])
 * @method static mixed sendMessage(string $conversationId, $senderId, string $content, array $meta = [])
 * @method static iterable getMessages(string $conversationId, int $limit = 50, int $offset = 0)
 * 
 * @see \SimpleChat\Contracts\ChatDriver
 */
class SimpleChat extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ChatDriver::class;
    }
}
