<?php

namespace SimpleChat\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewConversationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $conversationId;
    public $messageContent;

    public function __construct($conversationId, $messageContent = null)
    {
        $this->conversationId = $conversationId;
        $this->messageContent = $messageContent;
    }

    public function build()
    {
        $subject = config('simple-chat.mode') === 'support'
            ? 'New Support Ticket Created'
            : 'New Chat Conversation Started';

        return $this->subject($subject)
            ->view('simple-chat::emails.new-conversation')
            ->with([
                'conversationId' => $this->conversationId,
                'messageContent' => $this->messageContent,
            ]);
    }
}
