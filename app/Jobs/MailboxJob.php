<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\MailService;

class MailboxJob implements ShouldQueue
{
    use Queueable;

    protected $policy;
    protected $details;

    /**
     * Create a new job instance.
     */
    public function __construct($policy, $details)
    {
        $this->policy = $policy;
        $this->details = $details;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!empty($this->details)) {
            $mailService = new MailService();
            $mailService->sendPolicyChangesMail($this->policy, $this->details);
        } else {
            $mailService = new MailService();
            $mailService->sendPolicyCreatedMail($this->policy);
        }
    }
}
