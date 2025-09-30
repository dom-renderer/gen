<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Policy;
use App\Mail\PolicyCreatedMail;

class PolicyCreatedNotification extends Notification
{
    use Queueable;

    public $policy;

    public function __construct(Policy $policy)
    {
        $this->policy = $policy;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): PolicyCreatedMail
    {
        return (new PolicyCreatedMail($this->policy, $notifiable));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'policy_id' => $this->policy->id,
            'policy_number' => $this->policy->policy_number,
            'message' => 'A new policy has been created: ' . $this->policy->policy_number
        ];
    }
}
