<?php

namespace App\Services;

use App\Mail\TemplateMail;
use App\Models\EmailTemplate;
use App\Models\ScheduledEmail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

class EmailService
{
    /**
     * Send email immediately using a template
     */
    public function sendEmail(EmailTemplate $template, string $recipientEmail, string $recipientName = '', array $variables = [], string $customMessage = ''): bool
    {
        try {
            Mail::to($recipientEmail)->send(new TemplateMail($template, $variables, $recipientName, $customMessage));
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email to all users using a template
     */
    public function sendEmailToAllUsers(EmailTemplate $template, array $variables = []): int
    {
        $users = User::all();
        $sentCount = 0;

        foreach ($users as $user) {
            if ($this->sendEmail($template, $user->email, $user->name, $variables)) {
                $sentCount++;
            }
        }

        return $sentCount;
    }

    /**
     * Schedule an email for later delivery
     */
    public function scheduleEmail(EmailTemplate $template, string $recipientEmail, \DateTime $scheduledAt, string $recipientName = '', array $variables = [], string $customMessage = ''): ScheduledEmail
    {
        return ScheduledEmail::create([
            'email_template_id' => $template->id,
            'recipient_email' => $recipientEmail,
            'recipient_name' => $recipientName,
            'template_variables' => $variables,
            'custom_message' => $customMessage,
            'scheduled_at' => $scheduledAt,
            'status' => 'pending',
        ]);
    }

    /**
     * Schedule email to all users
     */
    public function scheduleEmailToAllUsers(EmailTemplate $template, \DateTime $scheduledAt, array $variables = []): int
    {
        $users = User::all();
        $scheduledCount = 0;

        foreach ($users as $user) {
            $this->scheduleEmail($template, $user->email, $scheduledAt, $user->name, $variables);
            $scheduledCount++;
        }

        return $scheduledCount;
    }

    /**
     * Process scheduled emails (to be called by a cron job)
     */
    public function processScheduledEmails(): int
    {
        $scheduledEmails = ScheduledEmail::where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->with('emailTemplate')
            ->get();

        $processedCount = 0;

        foreach ($scheduledEmails as $scheduledEmail) {
            try {
                $this->sendEmail(
                    $scheduledEmail->emailTemplate,
                    $scheduledEmail->recipient_email,
                    $scheduledEmail->recipient_name,
                    $scheduledEmail->template_variables ?? [],
                    $scheduledEmail->custom_message ?? ''
                );

                $scheduledEmail->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                $processedCount++;
            } catch (\Exception $e) {
                $scheduledEmail->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }
        }

        return $processedCount;
    }

    /**
     * Cancel a scheduled email
     */
    public function cancelScheduledEmail(ScheduledEmail $scheduledEmail): bool
    {
        if ($scheduledEmail->isPending()) {
            $scheduledEmail->update(['status' => 'cancelled']);
            return true;
        }

        return false;
    }

    /**
     * Get available email templates
     */
    public function getActiveTemplates(): \Illuminate\Database\Eloquent\Collection
    {
        return EmailTemplate::where('is_active', true)->get();
    }

    /**
     * Get scheduled emails by status
     */
    public function getScheduledEmailsByStatus(string $status): \Illuminate\Database\Eloquent\Collection
    {
        return ScheduledEmail::where('status', $status)
            ->with('emailTemplate')
            ->orderBy('scheduled_at')
            ->get();
    }
}
