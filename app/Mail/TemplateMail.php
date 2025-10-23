<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TemplateMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public EmailTemplate $template;
    public array $variables;
    public string $recipientName;
    public string $customMessage;

    /**
     * Create a new message instance.
     */
    public function __construct(EmailTemplate $template, array $variables = [], string $recipientName = '', string $customMessage = '')
    {
        $this->template = $template;
        $this->variables = $variables;
        $this->recipientName = $recipientName;
        $this->customMessage = $customMessage;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->template->subject;

        // Replace variables in subject
        foreach ($this->variables as $key => $value) {
            $subject = str_replace('{{' . $key . '}}', $value, $subject);
        }

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $htmlContent = $this->template->html_content;

        // Replace variables in HTML content
        foreach ($this->variables as $key => $value) {
            $htmlContent = str_replace('{{' . $key . '}}', $value, $htmlContent);
        }

        // Add custom message if provided
        if (!empty($this->customMessage)) {
            $customMessageHtml = '<div style="margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-left: 4px solid #007bff; border-radius: 4px;">';
            $customMessageHtml .= '<h4 style="margin: 0 0 10px 0; color: #007bff;">Custom Message</h4>';
            $customMessageHtml .= '<p style="margin: 0; white-space: pre-wrap;">' . htmlspecialchars($this->customMessage) . '</p>';
            $customMessageHtml .= '</div>';

            // Insert custom message before the closing body tag or at the end
            if (strpos($htmlContent, '</body>') !== false) {
                $htmlContent = str_replace('</body>', $customMessageHtml . '</body>', $htmlContent);
            } else {
                $htmlContent .= $customMessageHtml;
            }
        }

        return new Content(
            htmlString: $htmlContent,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
