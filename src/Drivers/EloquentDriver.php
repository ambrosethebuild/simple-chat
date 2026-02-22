<?php

namespace SimpleChat\Drivers;

use SimpleChat\Contracts\ChatDriver;
use SimpleChat\Models\Conversation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EloquentDriver implements ChatDriver
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getConversation(string $id, array $participants = [])
    {
        return Conversation::firstOrCreate(
            ['id' => $id],
            ['participants' => $participants]
        );
    }

    public function sendMessage(string $conversationId, $senderId, string $content, array $meta = [])
    {
        $table = $this->config['table_messages'];

        $id = (string) Str::uuid();

        DB::table($table)->insert([
            'id' => $id,
            'conversation_id' => $conversationId,
            'sender_id' => $senderId,
            'content' => $content,
            'meta' => json_encode($meta),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return DB::table($table)->where('id', $id)->first();
    }

    public function getMessages(string $conversationId, int $limit = 50, int $offset = 0)
    {
        $table = $this->config['table_messages'];

        return DB::table($table)
            ->where('conversation_id', $conversationId)
            ->latest('created_at')
            ->skip($offset)
            ->take($limit)
            ->get();
    }
}
