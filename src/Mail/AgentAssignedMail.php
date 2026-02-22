<?php

namespace SimpleChat\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AgentAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $conversationId;
    public $agentId;

    public function __construct($conversationId, $agentId)
    {
        $this->conversationId = $conversationId;
        $this->agentId = $agentId;
    }

    public function build()
    {
        return $this->subject('An Agent has been Assigned to your Ticket')
            ->view('simple-chat::emails.agent-assigned')
            ->with([
                'conversationId' => $this->conversationId,
                'agentId' => $this->agentId,
            ]);
    }
}
