<?php

namespace App\Filament\Pages;

use App\Models\EmailTemplate;
use App\Models\Enrollment;
use App\Services\EmailService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Radio;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class SendEmails extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-paper-airplane';
    protected string $view = 'filament.pages.send-emails';
    protected static ?string $title = 'Send & Schedule Emails';
    protected static ?string $navigationLabel = 'Send Emails';

    public static function getNavigationGroup(): ?string
    {
        return 'Email System';
    }

    public array $data = [
        'template_id' => null,
        'send_type' => 'immediate',
        'recipient_type' => 'all_enrollments',
        'enrollment_ids' => [],
        'custom_email' => '',
        'custom_name' => '',
        'custom_message' => '',
        'scheduled_at' => null,
    ];

    public function mount(): void
    {
        // Initialize form with default values to prevent validation errors on load
        $this->form->fill($this->data);

        // Force form to update its state
        $this->form->getState();
    }

    public function updatedRecipientType(): void
    {
        // Form will automatically re-render when recipient type changes
    }

    public function updatedTemplateId($value): void
    {
        // Update the template ID in data
        $this->data['template_id'] = $value;

        // Clear all existing template variable fields
        $template = EmailTemplate::find($value);
        if ($template) {
            $variables = $template->getAvailableVariables();
            foreach ($variables as $variable) {
                $varName = is_array($variable) ? $variable['name'] : $variable;
                $this->data["template_variable_{$varName}"] = '';
            }
        }
    }
    /**
     * Process template variables from form data
     */
    private function processTemplateVariables($data): array
    {
        $variables = [];

        // Handle individual template variable fields
        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'template_variable_') && !empty($value)) {
                $varName = str_replace('template_variable_', '', $key);
                $variables[$varName] = $value;
            }
        }

        return $variables;
    }

    /**
     * Get template variable fields dynamically
     */
    private function getTemplateVariableFields(): array
    {
        $fields = [];

        // Get template ID from the current form state
        $templateId = $this->data['template_id'] ?? null;

        if ($templateId) {
            $template = EmailTemplate::find($templateId);
            if ($template) {
                $variables = $template->getAvailableVariables();

                foreach ($variables as $variable) {
                    // Handle both string and object formats
                    if (is_array($variable)) {
                        $varName = $variable['name'] ?? $variable;
                        $varDescription = $variable['description'] ?? '';
                    } else {
                        $varName = $variable;
                        $varDescription = '';
                    }

                    $fields[] = TextInput::make("template_variable_{$varName}")
                        ->label(ucwords(str_replace('_', ' ', $varName)))
                        ->placeholder("Enter value for {$varName}")
                        ->helperText($varDescription ?: "This will replace {{$varName}} in your email")
                        ->live();
                }
            }
        }

        return $fields;
    }

    public function form(Schema $schema): Schema
    {
        $components = [
            Select::make('template_id')
                ->label('Email Template')
                ->options(EmailTemplate::where('is_active', true)->pluck('name', 'id'))
                ->placeholder('Select an email template')
                ->helperText('Choose the email template you want to send')
                ->live()
                ->afterStateUpdated(function ($state) {
                    $this->updatedTemplateId($state);
                }),

            // Dynamic template variable fields in collapsible section
            Section::make('Template Variables')
                ->description('Customize the template variables for this email')
                ->schema($this->getTemplateVariableFields())
                ->collapsible()
                ->collapsed()
                ->visible(fn($get) => !empty($get('template_id')))
                ->columns(2),

            Textarea::make('custom_message')
                ->label('Custom Message Body')
                ->placeholder('Enter a custom message to include in the email...')
                ->helperText('This message will be added to the email template. Leave empty to use only the template.')
                ->rows(4)
                ->columnSpanFull(),

            Radio::make('recipient_type')
                ->label('Recipients')
                ->options([
                    'all_enrollments' => 'All Enrollments',
                    'specific_enrollments' => 'Specific Enrollments',
                    'custom_email' => 'Custom Email Address',
                ])
                ->default('all_enrollments')
                ->inline()
                ->live()
                ->helperText('Choose who should receive this email.'),

            Select::make('enrollment_ids')
                ->label('Select Enrollments')
                ->multiple()
                ->options(Enrollment::pluck('guest_name', 'id'))
                ->visible(fn($get) => $get('recipient_type') === 'specific_enrollments')
                ->required(fn($get) => $get('recipient_type') === 'specific_enrollments')
                ->live()
                ->helperText('Select specific enrollments to receive this email'),

            TextInput::make('custom_email')
                ->label('Email Address')
                ->email()
                ->visible(fn($get) => $get('recipient_type') === 'custom_email')
                ->required(fn($get) => $get('recipient_type') === 'custom_email')
                ->live()
                ->helperText('Enter the email address to send to'),

            TextInput::make('custom_name')
                ->label('Recipient Name')
                ->visible(fn($get) => $get('recipient_type') === 'custom_email')
                ->required(fn($get) => $get('recipient_type') === 'custom_email')
                ->live()
                ->helperText('Enter the recipient\'s name'),

            Radio::make('send_type')
                ->label('Send Type')
                ->options([
                    'immediate' => 'Send Immediately',
                    'schedule' => 'Schedule for Later',
                ])
                ->default('immediate')
                ->inline()
                ->live()
                ->helperText('Choose when to send the email'),

            DateTimePicker::make('scheduled_at')
                ->label('Schedule Date & Time')
                ->visible(fn($get) => $get('send_type') === 'schedule')
                ->minDate(fn() => now())
                ->seconds(false)
                ->displayFormat('M j, Y g:i A')
                ->default(now()->addHour())
                ->helperText('Select when to send the email'),

        ];

        return $schema->components($components)->statePath('data')->columns(1);
    }


    protected function getActions(): array
    {
        return [
            Action::make('send')
                ->label('Send Email Now')
                ->color('success')
                ->action('sendEmail')
                ->requiresConfirmation()
                ->modalHeading('Send Email')
                ->modalDescription('Are you sure you want to send this email immediately?')
                ->modalSubmitActionLabel('Yes, Send Email'),

            Action::make('schedule')
                ->label('Schedule Email')
                ->color('warning')
                ->action('scheduleEmail')
                ->requiresConfirmation()
                ->modalHeading('Schedule Email')
                ->modalDescription('Are you sure you want to schedule this email for later?')
                ->modalSubmitActionLabel('Yes, Schedule Email'),

            // Action::make('preview')
            //     ->label('Preview Email')
            //     ->color('info')
            //     ->action('previewEmail')
            //     ->modalHeading('Email Preview')
            //     ->modalContent(fn() => $this->getEmailPreview())
            //     ->modalSubmitAction(false)
            //     ->modalCancelActionLabel('Close'),
        ];
    }

    public function sendEmail(): void
    {
        // Get form data first
        $data = $this->form->getState();

        // Validate the form
        $this->form->validate([
            'template_id' => 'required|exists:email_templates,id',
            'recipient_type' => 'required',
            'send_type' => 'required',
            'enrollment_ids' => 'required_if:recipient_type,specific_enrollments',
            'custom_email' => 'required_if:recipient_type,custom_email|email',
            'custom_name' => 'required_if:recipient_type,custom_email',
        ]);
        $data = $this->form->getState();

        if ($data['send_type'] !== 'immediate') {
            Notification::make()
                ->title('Please select "Send Immediately" to send emails now!')
                ->warning()
                ->send();
            return;
        }

        $template = EmailTemplate::find($data['template_id']);

        if (!$template) {
            Notification::make()
                ->title('Template not found!')
                ->danger()
                ->send();
            return;
        }


        // Get custom template variables if provided
        $customTemplateVariables = $this->processTemplateVariables($data);

        $emailService = app(EmailService::class);

        try {
            if ($data['recipient_type'] === 'all_enrollments') {
                $enrollments = Enrollment::all();
                $sentCount = 0;
                foreach ($enrollments as $enrollment) {
                    // Use custom variables if provided, otherwise use enrollment data
                    if (!empty($customTemplateVariables)) {
                        $enrollmentVariables = $customTemplateVariables;
                    } else {
                        $enrollmentVariables = $this->populateVariablesFromEnrollment($template->getAvailableVariables(), $enrollment);
                    }
                    if ($emailService->sendEmail($template, $enrollment->guest_email, $enrollment->guest_name, $enrollmentVariables, $data['custom_message'] ?? '')) {
                        $sentCount++;
                    }
                    // Add delay to prevent rate limiting
                    usleep(500000); // 0.5 second delay
                }
                Notification::make()
                    ->title("Email sent to {$sentCount} enrollments!")
                    ->success()
                    ->send();
            } elseif ($data['recipient_type'] === 'specific_enrollments') {
                $enrollments = Enrollment::whereIn('id', $data['enrollment_ids'])->get();
                $sentCount = 0;
                foreach ($enrollments as $enrollment) {
                    // Use custom variables if provided, otherwise use enrollment data
                    if (!empty($customTemplateVariables)) {
                        $enrollmentVariables = $customTemplateVariables;
                    } else {
                        $enrollmentVariables = $this->populateVariablesFromEnrollment($template->getAvailableVariables(), $enrollment);
                    }
                    if ($emailService->sendEmail($template, $enrollment->guest_email, $enrollment->guest_name, $enrollmentVariables, $data['custom_message'] ?? '')) {
                        $sentCount++;
                    }
                    // Add delay to prevent rate limiting
                    usleep(500000); // 0.5 second delay
                }
                Notification::make()
                    ->title("Email sent to {$sentCount} enrollments!")
                    ->success()
                    ->send();
            } elseif ($data['recipient_type'] === 'custom_email') {
                // For custom email, use custom template variables if provided, otherwise basic variables
                if (!empty($customTemplateVariables)) {
                    $customVariables = $customTemplateVariables;
                } else {
                    $customVariables = [
                        'name' => $data['custom_name'] ?? '',
                        'email' => $data['custom_email'],
                    ];
                }
                if ($emailService->sendEmail($template, $data['custom_email'], $data['custom_name'] ?? '', $customVariables, $data['custom_message'] ?? '')) {
                    Notification::make()
                        ->title('Email sent successfully!')
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('Failed to send email!')
                        ->danger()
                        ->send();
                }
            }
        } catch (\Exception $e) {
            // Check if it's a rate limiting error
            if (str_contains($e->getMessage(), 'Too many emails per second')) {
                Notification::make()
                    ->title('Rate Limit Exceeded')
                    ->body('Too many emails sent too quickly. Please wait a moment and try again, or send to fewer recipients.')
                    ->warning()
                    ->send();
            } else {
                Notification::make()
                    ->title('Error: ' . $e->getMessage())
                    ->danger()
                    ->send();
            }
        }
    }

    public function scheduleEmail(): void
    {
        // Validate the form
        $this->form->validate([
            'template_id' => 'required|exists:email_templates,id',
            'recipient_type' => 'required',
            'send_type' => 'required',
            'scheduled_at' => 'required_if:send_type,schedule',
            'enrollment_ids' => 'required_if:recipient_type,specific_enrollments',
            'custom_email' => 'required_if:recipient_type,custom_email|email',
            'custom_name' => 'required_if:recipient_type,custom_email',
        ]);
        $data = $this->form->getState();

        if ($data['send_type'] !== 'schedule') {
            Notification::make()
                ->title('Please select "Schedule for Later" to schedule emails!')
                ->warning()
                ->send();
            return;
        }

        $template = EmailTemplate::find($data['template_id']);

        if (!$template) {
            Notification::make()
                ->title('Template not found!')
                ->danger()
                ->send();
            return;
        }


        // Get custom template variables if provided
        $customTemplateVariables = $this->processTemplateVariables($data);

        $emailService = app(EmailService::class);

        try {
            if ($data['recipient_type'] === 'all_enrollments') {
                $enrollments = Enrollment::all();
                $scheduledCount = 0;
                $scheduledAt = new \DateTime($data['scheduled_at']);
                foreach ($enrollments as $enrollment) {
                    // Use custom variables if provided, otherwise use enrollment data
                    if (!empty($customTemplateVariables)) {
                        $enrollmentVariables = $customTemplateVariables;
                    } else {
                        $enrollmentVariables = $this->populateVariablesFromEnrollment($template->getAvailableVariables(), $enrollment);
                    }
                    $emailService->scheduleEmail($template, $enrollment->guest_email, $scheduledAt, $enrollment->guest_name, $enrollmentVariables, $data['custom_message'] ?? '');
                    $scheduledCount++;
                    // Add delay to prevent rate limiting
                    usleep(500000); // 0.5 second delay
                }
                Notification::make()
                    ->title("Email scheduled for {$scheduledCount} enrollments!")
                    ->success()
                    ->send();
            } elseif ($data['recipient_type'] === 'specific_enrollments') {
                $enrollments = Enrollment::whereIn('id', $data['enrollment_ids'])->get();
                $scheduledCount = 0;
                $scheduledAt = new \DateTime($data['scheduled_at']);
                foreach ($enrollments as $enrollment) {
                    // Use custom variables if provided, otherwise use enrollment data
                    if (!empty($customTemplateVariables)) {
                        $enrollmentVariables = $customTemplateVariables;
                    } else {
                        $enrollmentVariables = $this->populateVariablesFromEnrollment($template->getAvailableVariables(), $enrollment);
                    }
                    $emailService->scheduleEmail($template, $enrollment->guest_email, $scheduledAt, $enrollment->guest_name, $enrollmentVariables, $data['custom_message'] ?? '');
                    $scheduledCount++;
                    // Add delay to prevent rate limiting
                    usleep(500000); // 0.5 second delay
                }
                Notification::make()
                    ->title("Email scheduled for {$scheduledCount} enrollments!")
                    ->success()
                    ->send();
            } elseif ($data['recipient_type'] === 'custom_email') {
                // Use custom template variables if provided, otherwise basic variables
                if (!empty($customTemplateVariables)) {
                    $customVariables = $customTemplateVariables;
                } else {
                    $customVariables = [
                        'name' => $data['custom_name'] ?? '',
                        'email' => $data['custom_email'],
                    ];
                }
                $scheduledAt = new \DateTime($data['scheduled_at']);
                $emailService->scheduleEmail($template, $data['custom_email'], $scheduledAt, $data['custom_name'] ?? '', $customVariables, $data['custom_message'] ?? '');
                Notification::make()
                    ->title('Email scheduled successfully!')
                    ->success()
                    ->send();
            }
        } catch (\Exception $e) {
            // Check if it's a rate limiting error
            if (str_contains($e->getMessage(), 'Too many emails per second')) {
                Notification::make()
                    ->title('Rate Limit Exceeded')
                    ->body('Too many emails sent too quickly. Please wait a moment and try again, or send to fewer recipients.')
                    ->warning()
                    ->send();
            } else {
                Notification::make()
                    ->title('Error: ' . $e->getMessage())
                    ->danger()
                    ->send();
            }
        }
    }

    /**
     * Get template variables populated with enrollment data
     */
    private function getTemplateVariables($template, $data): array
    {
        $variables = [];
        $templateVariables = $template->getAvailableVariables();

        // For each enrollment, populate variables with their data
        if ($data['recipient_type'] === 'all_enrollments') {
            $enrollments = Enrollment::all();
        } elseif ($data['recipient_type'] === 'specific_enrollments') {
            $enrollments = Enrollment::whereIn('id', $data['enrollment_ids'])->get();
        } else {
            // For custom email, we'll handle this differently
            return [];
        }

        // Get the first enrollment to populate variables (for preview purposes)
        if ($enrollments->count() > 0) {
            $enrollment = $enrollments->first();
            $variables = $this->populateVariablesFromEnrollment($templateVariables, $enrollment);
        }

        return $variables;
    }

    /**
     * Preview email with custom variables
     */
    public function previewEmail(): void
    {
        $data = $this->form->getState();

        if (empty($data['template_id'])) {
            Notification::make()
                ->title('Please select a template first!')
                ->warning()
                ->send();
            return;
        }

        $template = EmailTemplate::find($data['template_id']);
        if (!$template) {
            Notification::make()
                ->title('Template not found!')
                ->danger()
                ->send();
            return;
        }

        // This will be handled by the modal content method
    }

    /**
     * Get email preview content
     */
    public function getEmailPreview(): string
    {
        $data = $this->form->getState();
        $template = EmailTemplate::find($data['template_id']);

        if (!$template) {
            return '<p>Template not found!</p>';
        }

        // Get custom template variables
        $customTemplateVariables = $this->processTemplateVariables($data);

        // If no custom variables, use sample data
        if (empty($customTemplateVariables)) {
            $customTemplateVariables = [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'company_name' => 'ESO Training',
                'course_name' => 'Sample Course',
                'course_date' => '2024-01-15',
                'course_duration' => 'Full Day',
                'course_location' => 'ESO Training Center',
                'instructor_name' => 'Professional Instructor',
                'booking_reference' => 'ESO-000001',
                'course_price' => '£199',
                'student_name' => 'John Doe',
                'message_title' => 'Important Announcement',
                'message_content' => 'This is a sample message content.',
                'button_text' => 'Click Here',
                'button_link' => 'https://example.com',
            ];
        }

        // Replace variables in subject
        $subject = $template->subject;
        foreach ($customTemplateVariables as $key => $value) {
            $subject = str_replace('{{' . $key . '}}', $value, $subject);
        }

        // Replace variables in content
        $content = $template->html_content;
        foreach ($customTemplateVariables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }

        return "
            <div style='max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;'>
                <h3 style='color: #333; margin-bottom: 20px;'>Email Preview</h3>
                <div style='margin-bottom: 20px;'>
                    <strong>Subject:</strong> {$subject}
                </div>
                <div style='border-top: 1px solid #eee; padding-top: 20px;'>
                    <strong>Content:</strong>
                    <div style='margin-top: 10px; padding: 15px; background: #f9f9f9; border-radius: 4px;'>
                        {$content}
                    </div>
                </div>
            </div>
        ";
    }

    /**
     * Populate template variables with enrollment data
     */
    private function populateVariablesFromEnrollment($templateVariables, $enrollment): array
    {
        $variables = [];

        foreach ($templateVariables as $variable) {
            $varName = is_array($variable) ? $variable['name'] : $variable;

            // Map common variables to enrollment data
            switch ($varName) {
                case 'name':
                case 'student_name':
                    $variables[$varName] = $enrollment->guest_name;
                    break;
                case 'email':
                    $variables[$varName] = $enrollment->guest_email;
                    break;
                case 'company_name':
                    $variables[$varName] = 'ESO Training';
                    break;
                case 'course_name':
                    $variables[$varName] = $enrollment->course->name ?? 'Course';
                    break;
                case 'course_date':
                    $variables[$varName] = $enrollment->course_date ?? 'TBD';
                    break;
                case 'course_duration':
                    $variables[$varName] = $enrollment->course->duration ?? 'Full Day';
                    break;
                case 'course_location':
                    $variables[$varName] = $enrollment->course->location ?? 'ESO Training Center';
                    break;
                case 'instructor_name':
                    $variables[$varName] = $enrollment->course->instructor ?? 'Professional Instructor';
                    break;
                case 'booking_reference':
                    $variables[$varName] = 'ESO-' . str_pad($enrollment->id, 6, '0', STR_PAD_LEFT);
                    break;
                case 'course_price':
                    $variables[$varName] = '£' . ($enrollment->course->price ?? '199');
                    break;
                // Promotional email variables
                case 'discount_percentage':
                    $variables[$varName] = '25'; // Default discount
                    break;
                case 'offer_description':
                    $variables[$varName] = 'Get 25% off your next course enrollment!';
                    break;
                case 'offer_link':
                    $variables[$varName] = 'https://eso.test/courses';
                    break;
                case 'expiry_date':
                    $variables[$varName] = now()->addDays(7)->format('M j, Y');
                    break;
                default:
                    // For unknown variables, use a placeholder
                    $variables[$varName] = "[$varName]";
                    break;
            }
        }

        return $variables;
    }
}
