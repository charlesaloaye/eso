<?php

namespace App\Filament\Resources\ScheduledEmails\Pages;

use App\Filament\Resources\ScheduledEmails\ScheduledEmailResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListScheduledEmails extends ListRecords
{
    protected static string $resource = ScheduledEmailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
