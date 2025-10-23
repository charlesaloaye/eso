<?php

namespace App\Filament\Widgets;

use App\Models\Email;
use App\Models\ScheduledEmail;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmailStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalEmails = Email::count();
        $pendingScheduled = ScheduledEmail::where('status', 'pending')->count();
        $sentScheduled = ScheduledEmail::where('status', 'sent')->count();

        return [
            Stat::make('Total Emails', $totalEmails)
                ->description('All time emails sent')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('success'),

            Stat::make('Pending Scheduled', $pendingScheduled)
                ->description('Emails waiting to be sent')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Sent Scheduled', $sentScheduled)
                ->description('Successfully sent emails')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info'),
        ];
    }
}
