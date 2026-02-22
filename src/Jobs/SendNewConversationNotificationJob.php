<?php

namespace SimpleChat\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use SimpleChat\Mail\NewConversationMail;

class SendNewConversationNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $conversationId;
    public $messageContent;

    public function __construct($conversationId, $messageContent = null)
    {
        $this->conversationId = $conversationId;
        $this->messageContent = $messageContent;
    }

    public function handle()
    {
        $emails = config('simple-chat.notifications.email_addresses');

        if (empty($emails)) {
            return;
        }

        $emailsArray = array_filter(array_map('trim', explode(',', (string) $emails)));

        if (count($emailsArray) > 0) {
            Mail::to($emailsArray)->send(new NewConversationMail($this->conversationId, $this->messageContent));
        }
    }
}
