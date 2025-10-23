<?php

namespace App\Filament\Resources\ScheduledEmails\Pages;

use App\Filament\Resources\ScheduledEmails\ScheduledEmailResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditScheduledEmail extends EditRecord
{
    protected static string $resource = ScheduledEmailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
