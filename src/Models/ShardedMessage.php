<?php

namespace SimpleChat\Models;

use Illuminate\Database\Eloquent\Model;

class ShardedMessage extends Model
{
    protected $connection = 'simple_chat_shard';
    protected $table = 'messages';
    protected $guarded = [];

    // The timestamp handling might need to be adjusted for SQLite default formats if strict,
    // but Laravel usually handles this well.
}
