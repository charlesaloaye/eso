<?php

namespace App\Filament\Widgets;

use App\Models\Enrollment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EnrollmentStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalEnrollments = Enrollment::count();
        $thisMonthEnrollments = Enrollment::whereMonth('created_at', now()->month)->count();
        $thisWeekEnrollments = Enrollment::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

        return [
            Stat::make('Total Enrollments', $totalEnrollments)
                ->description('All time enrollments')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('success'),

            Stat::make('This Month', $thisMonthEnrollments)
                ->description('Enrollments this month')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make('This Week', $thisWeekEnrollments)
                ->description('Enrollments this week')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
