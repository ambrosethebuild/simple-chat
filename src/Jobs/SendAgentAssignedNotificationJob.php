<?php

namespace SimpleChat\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use SimpleChat\Mail\AgentAssignedMail;
use SimpleChat\Models\Conversation;

class SendAgentAssignedNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $conversationId;
    public $agentId;

    public function __construct($conversationId, $agentId)
    {
        $this->conversationId = $conversationId;
        $this->agentId = $agentId;
    }

    public function handle()
    {
        $conversation = Conversation::find($this->conversationId);

        if (!$conversation) {
            return;
        }

        $participants = is_array($conversation->participants)
            ? $conversation->participants
            : json_decode($conversation->participants, true) ?? [];

        $userModelClass = config('auth.providers.users.model', \App\Models\User::class);

        if (!class_exists($userModelClass)) {
            return;
        }

        $users = $userModelClass::whereIn('id', $participants)->get();
        $emails = $users->pluck('email')->filter()->toArray();

        // Optional: exclude the agent who just assigned themselves if needed, but the requirement 
        // says "sending mail to conversation participants", assuming all of them.

        if (count($emails) > 0) {
            Mail::to($emails)->send(new AgentAssignedMail($this->conversationId, $this->agentId));
        }
    }
}
