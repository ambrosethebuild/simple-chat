<?php

namespace SimpleChat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'simple_chat_conversations';

    protected $guarded = [];

    protected $casts = [
        'participants' => 'array',
    ];

    /**
     * Get the User models associated with the participant IDs.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getUsersAttribute()
    {
        $userClass = config('auth.providers.users.model', '\\App\\Models\\User');
        $participantIds = $this->participants ?? [];

        if (empty($participantIds)) {
            return collect();
        }

        return $userClass::whereIn('id', $participantIds)->get();
    }

    public function getOtherUsersAttribute()
    {
        $userClass = config('auth.providers.users.model', '\App\Models\User');
        $participantIds = $this->participants ?? [];

        if (empty($participantIds)) {
            return collect();
        }

        return $userClass::whereIn('id', $participantIds)->where('id', '!=', auth()->id())->get();
    }

    /**
     * Get a formatted string of the participant names.
     * Fallbacks to their original IDs if the User models cannot be resolved.
     *
     * @return string
     */
    public function getParticipantNamesAttribute()
    {
        $users = $this->users;

        // If we have resolved User models with 'name' attributes, use them
        if ($users->isNotEmpty() && $users->first()->offsetExists('name')) {
            return $users->pluck('name')->filter()->implode(', ');
        }

        // Fallback: Just return the raw ID array joined by commas
        $fallback = is_array($this->participants) ? $this->participants : json_decode($this->participants ?? '[]', true);
        return implode(', ', $fallback ?: []);
    }
}
