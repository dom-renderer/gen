<?php

namespace App\Services;

use App\Models\Mail;
use App\Models\User;
use App\Models\Policy;
use App\Notifications\PolicyChangedNotification;
use App\Notifications\PolicyCreatedNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class MailService
{
    public function sendPolicyCreatedMail(Policy $policy)
    {
        $clientServiceTeamUsers = User::role('client-service-team')->get();
        
        if ($clientServiceTeamUsers->isEmpty()) {
            return;
        }

        foreach ($clientServiceTeamUsers as $user) {
            $mail = Mail::create([
                'subject' => 'New Policy Created - ' . $policy->policy_number,
                'body' => $this->generatePolicyCreatedBody($policy, $user),
                'from_email' => config('mail.from.address'),
                'from_name' => config('mail.from.name'),
                'to_email' => $user->email,
                'to_name' => $user->name,
                'type' => 'policy_created',
                'metadata' => [
                    'policy_id' => $policy->id,
                    'policy_number' => $policy->policy_number,
                    'created_by' => auth()->user()->name ?? 'System'
                ]
            ]);

            try {
                Notification::send($user, new PolicyCreatedNotification($policy));
            } catch (\Exception $e) {
                Log::error('Failed to send policy created notification: ' . $e->getMessage());
            }
        }
    }

    public function sendPolicyChangesMail(Policy $policy, $details = [])
    {
        $clientServiceTeamUsers = User::role('client-service-team')->whereHas('policyManager', function ($buider) use ($policy) {
            $buider->where('policy_id', $policy->id);
        })->get();
        
        if ($clientServiceTeamUsers->isEmpty()) {
            return;
        }

        foreach ($clientServiceTeamUsers as $user) {
            Mail::create([
                'subject' => (isset($details['title']) ? $details['title'] : 'Policy') . ' Changes - ' . $policy->policy_number,
                'body' => $this->generatePolicyCreatedBody($policy, $user),
                'from_email' => config('mail.from.address'),
                'from_name' => config('mail.from.name'),
                'to_email' => $user->email,
                'to_name' => $user->name,
                'type' => 'policy_created',
                'metadata' => [
                    'policy_id' => $policy->id,
                    'policy_number' => $policy->policy_number,
                    'created_by' => auth()->user()->name ?? 'System'
                ]
            ]);

            try {
                Notification::send($user, new PolicyChangedNotification($policy, $details));
            } catch (\Exception $e) {
                Log::error('Failed to send policy created notification: ' . $e->getMessage());
            }
        }
    }

    private function generatePolicyCreatedBody(Policy $policy, User $user)
    {
        $holderName = $policy->holders && $policy->holders->count() > 0 ? $policy->holders->first()->name : 'N/A';
        $introducerName = $policy->introducers && $policy->introducers->count() > 0 ? $policy->introducers->first()->name : 'N/A';
        
        return view('emails.policy-created', compact('policy', 'user'))->render();
    }

    public function getUnreadCountForUser(User $user)
    {
        return Mail::where('to_email', $user->email)
            ->where('is_read', false)
            ->count();
    }
}
