<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalUsers = User::count();
        $thisMonthUsers = User::whereMonth('created_at', now()->month)->count();
        $activeUsers = User::whereNotNull('email_verified_at')->count();

        return [
            Stat::make('Total Users', $totalUsers)
                ->description('All registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('New This Month', $thisMonthUsers)
                ->description('Users registered this month')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('info'),

            Stat::make('Active Users', $activeUsers)
                ->description('Users with verified emails')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('warning'),
        ];
    }
}
