<?php

namespace App\Filament\Resources\ScheduledEmails\Tables;

use App\Services\EmailService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class ScheduledEmailsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('emailTemplate.name')
                    ->label('Template')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('recipient_email')
                    ->label('Recipient')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('recipient_name')
                    ->label('Recipient Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('scheduled_at')
                    ->label('Scheduled For')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'sent',
                        'danger' => 'failed',
                        'secondary' => 'cancelled',
                    ]),

                TextColumn::make('sent_at')
                    ->label('Sent At')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->placeholder('Not sent yet'),

                TextColumn::make('error_message')
                    ->label('Error')
                    ->limit(50)
                    ->placeholder('No errors'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->action(function ($record, EmailService $emailService) {
                        if ($emailService->cancelScheduledEmail($record)) {
                            Notification::make()
                                ->title('Email cancelled successfully!')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Cannot cancel this email!')
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn($record) => $record->isPending()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
