<?php

namespace App\Filament\Resources\Emails\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmailsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('sent_to')
                    ->label('Sent To')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('scheduled_at')
                    ->label('Scheduled At')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->wrap(),
                TextColumn::make('is_read')
                    ->label('Read Status')
                    ->sortable()
                    ->wrap(),   
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
