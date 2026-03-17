<?php

namespace SimpleChat\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $conversation;
    public $sender;
    public $messageContent;

    public function __construct($conversation, $sender, $messageContent)
    {
        $this->conversation = $conversation;
        $this->sender = $sender;
        $this->messageContent = $messageContent;
    }

    public function build()
    {
        $subject = 'New message in conversation #' . substr($this->conversation->id, 0, 8);

        return $this->subject($subject)
            ->view('simple-chat::emails.new-message')
            ->with([
                'conversation' => $this->conversation,
                'sender' => $this->sender,
                'messageContent' => $this->messageContent,
            ]);
    }
}
