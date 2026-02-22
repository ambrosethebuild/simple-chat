<?php

namespace SimpleChat\Drivers;

use SimpleChat\Contracts\ChatDriver;
use SimpleChat\Models\Conversation;
use SimpleChat\Models\ShardedMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class SqliteShardedDriver implements ChatDriver
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getConversation(string $id, array $participants = [])
    {
        // 1. Find or Create the Metadata Record in Main DB
        $conversation = Conversation::withTrashed()->firstOrCreate(
            ['id' => $id],
            ['participants' => $participants]
        );

        // 2. Determine Storage Path
        if (!$conversation->storage_path) {
            $fileName = "{$id}.sqlite";
            $path = rtrim($this->config['storage_path'], '/') . '/' . $fileName;

            // Ensure directory exists
            if (!is_dir(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            $conversation->update(['storage_path' => $path]);
        }

        // 3. Initialize the SQLite file if missing
        $this->ensureDatabaseExists($conversation->storage_path);

        return $conversation;
    }

    public function sendMessage(string $conversationId, $senderId, string $content, array $meta = [])
    {
        $conversation = $this->getConversation($conversationId);

        $this->connectToShard($conversation->storage_path);

        return ShardedMessage::create([
            'sender_id' => $senderId,
            'content' => $content,
            'meta' => json_encode($meta),
        ]);
    }

    public function getMessages(string $conversationId, int $limit = 50, int $offset = 0)
    {
        $conversation = $this->getConversation($conversationId);

        $this->connectToShard($conversation->storage_path);

        return ShardedMessage::latest()
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    protected function ensureDatabaseExists(string $path)
    {
        if (!file_exists($path)) {
            touch($path);

            // We need to initialize the schema.
            // We temporarily connect to run the creation query.
            $this->connectToShard($path);

            $schema = "
                CREATE TABLE IF NOT EXISTS messages (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    sender_id VARCHAR(255) NOT NULL,
                    content TEXT,
                    meta TEXT,
                    created_at DATETIME,
                    updated_at DATETIME
                );
            ";

            DB::connection('simple_chat_shard')->statement($schema);
        }
    }

    protected function connectToShard(string $path)
    {
        // Hot-swap the connection config
        Config::set('database.connections.simple_chat_shard', [
            'driver' => 'sqlite',
            'database' => $path,
            'foreign_key_constraints' => true,
        ]);

        // Purge to allow re-connection with new config
        DB::purge('simple_chat_shard');
        DB::reconnect('simple_chat_shard');
    }
}
