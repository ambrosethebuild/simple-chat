<?php

namespace SimpleChat\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use SimpleChat\Mail\NewMessageMail;
use SimpleChat\Models\Conversation;

class SendNewMessageNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $conversationId;
    public $senderId;
    public $messageContent;

    public function __construct($conversationId, $senderId, $messageContent)
    {
        $this->conversationId = $conversationId;
        $this->senderId = (string) $senderId;
        $this->messageContent = $messageContent;
    }

    public function handle()
    {
        $conversation = Conversation::withTrashed()->find($this->conversationId);
        if (!$conversation) {
            return;
        }

        $participants = $conversation->participants;
        if (empty($participants)) {
            return;
        }

        // Email throttle: skip if an email was already sent for this conversation
        // within the configured window to prevent flooding in active chats.
        $throttleMinutes = (int) config('simple-chat.notifications.email_throttle_minutes', 5);
        if ($throttleMinutes > 0) {
            $cacheKey = 'simple-chat.email-throttle.' . $this->conversationId;
            if (Cache::has($cacheKey)) {
                return;
            }
            Cache::put($cacheKey, true, now()->addMinutes($throttleMinutes));
        }

        $creatorId = (string) $participants[0];
        $isCreator = $this->senderId === $creatorId;

        $userClass = config('auth.providers.users.model', '\\App\\Models\\User');
        $sender = $userClass::find($this->senderId);

        if ($isCreator) {
            // Sent by Client -> Notify Support Team
            $emails = config('simple-chat.notifications.email_addresses');
            if (!empty($emails)) {
                $emailsArray = array_filter(array_map('trim', explode(',', (string) $emails)));
                if (count($emailsArray) > 0) {
                    Mail::to($emailsArray)->send(new NewMessageMail($conversation, $sender, $this->messageContent));
                }
            }
        } else {
            // Sent by Agent (or someone else) -> Notify Client (Creator)
            $creator = $userClass::find($creatorId);
            if ($creator && $creator->email) {
                Mail::to($creator->email)->send(new NewMessageMail($conversation, $sender, $this->messageContent));
            }
        }
    }
}
