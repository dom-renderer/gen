<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Mail\PolicyChangedMail;
use App\Models\Policy;

class PolicyChangedNotification extends Notification
{
    use Queueable;

    public $policy;
    public $details;

    /**
     * Create a new notification instance.
     */
    public function __construct(Policy $policy, $details)
    {
        $this->policy = $policy;
        $this->details = $details;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): PolicyChangedMail
    {
        return (new PolicyChangedMail($this->policy, $notifiable, $this->details));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'policy_id' => $this->policy->id,
            'policy_number' => $this->policy->policy_number,
            'message' => 'A policy has been changed: ' . $this->policy->policy_number
        ];
    }
}
