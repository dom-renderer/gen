<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Policy;

class PolicyCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $policy;
    public $user;

    public function __construct(Policy $policy, $user)
    {
        $this->policy = $policy;
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Policy Created - ' . $this->policy->policy_number,
            to: [$this->user->email],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.policy-created',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
