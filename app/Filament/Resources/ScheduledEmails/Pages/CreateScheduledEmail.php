<?php

namespace App\Filament\Resources\ScheduledEmails\Pages;

use App\Filament\Resources\ScheduledEmails\ScheduledEmailResource;
use Filament\Resources\Pages\CreateRecord;

class CreateScheduledEmail extends CreateRecord
{
    protected static string $resource = ScheduledEmailResource::class;
}
