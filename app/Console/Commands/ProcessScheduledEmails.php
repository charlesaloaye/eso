<?php

namespace App\Console\Commands;

use App\Services\EmailService;
use Illuminate\Console\Command;

class ProcessScheduledEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:process-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process scheduled emails that are due to be sent';

    /**
     * Execute the console command.
     */
    public function handle(EmailService $emailService)
    {
        $this->info('Processing scheduled emails...');

        $processedCount = $emailService->processScheduledEmails();

        $this->info("Processed {$processedCount} scheduled emails.");

        return Command::SUCCESS;
    }
}
