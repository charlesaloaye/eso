<?php

namespace App\Filament\Resources\Emails\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Schemas\Schema;

class EmailForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Email Details')->schema([
                    TextInput::make('email')
                        ->label('Email Address')
                        ->required()
                        ->email()
                        ->maxLength(255),

                ])->collapsible()->columnSpanFull(),


                Section::make('Content')->schema([
                    TextInput::make('subject')
                        ->label('Subject')
                        ->required()
                        ->maxLength(255),

                    MarkdownEditor::make('body')
                        ->label('Body')
                        ->required()
                        ->maxLength(65535),

                    Group::make([
                        Select::make('sent_to')
                            ->label('Send To')
                            ->options([
                                'all' => 'All Users',
                                'subscribers' => 'Subscribers',
                                'admins' => 'Admins',
                            ])
                            ->default('all')
                            ->required(),

                        DatePicker::make('scheduled_at')
                            ->label('Scheduled At')
                            ->nullable()
                            ->helperText('If not set, the email will be sent immediately.'),


                    ])->columns(2)

                ])->collapsible()->columnSpanFull()


            ])->columns(2);
    }
}
