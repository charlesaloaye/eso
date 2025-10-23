# Email Management System

This Laravel application includes a comprehensive email management system with Filament admin panel integration.

## Features

### 1. User Management

-   View all users in the admin panel
-   See user details including email verification status
-   Manage user information

### 2. Email Templates

-   Create and manage HTML email templates
-   Support for dynamic variables (e.g., `{{name}}`, `{{company_name}}`)
-   Rich text editor for HTML content
-   Template variables management

### 3. Email Sending

-   Send emails immediately using templates
-   Send to all users, specific users, or custom email addresses
-   Dynamic variable replacement in templates
-   Professional HTML email templates included

### 4. Email Scheduling

-   Schedule emails for future delivery
-   Manage scheduled emails
-   Cancel pending scheduled emails
-   Automatic processing via cron job

## Admin Panel Navigation

1. **Dashboard** - Main admin dashboard
2. **Users** - View and manage all users
3. **Email Templates** - Create and manage email templates
4. **Scheduled Emails** - View and manage scheduled emails
5. **Send Emails** - Send and schedule emails interface

## Pre-loaded Email Templates

The system comes with three professional email templates:

### 1. Welcome Email

-   Variables: `name`, `company_name`
-   Professional gradient design
-   Welcome message with next steps

### 2. Newsletter

-   Variables: `name`, `company_name`, `newsletter_title`, `newsletter_date`, `article1_title`, `article1_content`, `article2_title`, `article2_content`, `unsubscribe_link`
-   Multi-article layout
-   Newsletter-style design

### 3. Promotional Email

-   Variables: `name`, `company_name`, `discount_percentage`, `offer_description`, `offer_link`, `expiry_date`
-   Eye-catching promotional design
-   Call-to-action buttons

## Usage

### Sending Emails

1. Go to **Send Emails** in the admin panel
2. Select an email template
3. Choose recipients (All Users, Specific Users, or Custom Email)
4. Fill in template variables
5. Choose to send immediately or schedule for later
6. Click "Send Email" or "Schedule Email"

### Managing Templates

1. Go to **Email Templates** in the admin panel
2. Create new templates or edit existing ones
3. Use `{{variable_name}}` syntax for dynamic content
4. Define template variables for easy management

### Scheduling Emails

1. Use the **Send Emails** page to schedule emails
2. Set the date and time for delivery
3. View scheduled emails in **Scheduled Emails**
4. Cancel pending emails if needed

## Cron Job Setup

To process scheduled emails automatically, add this to your crontab:

```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

Or run manually:

```bash
php artisan emails:process-scheduled
```

## Technical Details

### Models

-   `User` - User management
-   `EmailTemplate` - Email templates with variables
-   `ScheduledEmail` - Scheduled email management

### Services

-   `EmailService` - Handles email sending and scheduling

### Mail Classes

-   `TemplateMail` - Dynamic template-based email sending

### Commands

-   `emails:process-scheduled` - Process scheduled emails

## File Structure

```
app/
├── Models/
│   ├── User.php
│   ├── EmailTemplate.php
│   └── ScheduledEmail.php
├── Services/
│   └── EmailService.php
├── Mail/
│   └── TemplateMail.php
├── Console/Commands/
│   └── ProcessScheduledEmails.php
└── Filament/
    ├── Resources/
    │   ├── Users/
    │   ├── EmailTemplates/
    │   └── ScheduledEmails/
    └── Pages/
        └── SendEmails.php
```

## Customization

### Adding New Templates

1. Create templates in the admin panel
2. Use HTML with CSS for styling
3. Define variables for dynamic content

### Customizing Email Design

-   Edit HTML content in templates
-   Use inline CSS for better email client compatibility
-   Test with different email clients

## Security Notes

-   Email templates are stored in the database
-   Variables are sanitized before replacement
-   Scheduled emails are processed securely
-   User data is protected with proper authentication

## Support

For issues or questions about the email system, check the Laravel and Filament documentation, or review the code in the respective model and service files.
