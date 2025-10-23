<?php

namespace App\Filament\Resources\ScheduledEmails;

use App\Filament\Resources\ScheduledEmails\Pages\CreateScheduledEmail;
use App\Filament\Resources\ScheduledEmails\Pages\EditScheduledEmail;
use App\Filament\Resources\ScheduledEmails\Pages\ListScheduledEmails;
use App\Filament\Resources\ScheduledEmails\Schemas\ScheduledEmailForm;
use App\Filament\Resources\ScheduledEmails\Tables\ScheduledEmailsTable;
use App\Models\ScheduledEmail;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ScheduledEmailResource extends Resource
{
    protected static ?string $model = ScheduledEmail::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    public static function getNavigationGroup(): ?string
    {
        return 'Email System';
    }

    public static function form(Schema $schema): Schema
    {
        return ScheduledEmailForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ScheduledEmailsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListScheduledEmails::route('/'),
            'create' => CreateScheduledEmail::route('/create'),
            'edit' => EditScheduledEmail::route('/{record}/edit'),
        ];
    }
}
