<?php

namespace App\Filament\Resources\EmailTemplates\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;

class EmailTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Template Name'),

                TextInput::make('subject')
                    ->required()
                    ->maxLength(255)
                    ->label('Email Subject')
                    ->helperText('Use {{variable_name}} for dynamic content'),

                RichEditor::make('html_content')
                    ->required()
                    ->label('HTML Content')
                    ->helperText('Use {{variable_name}} for dynamic content'),

                Repeater::make('variables')
                    ->label('Template Variables')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->label('Variable Name')
                            ->helperText('Without {{}} brackets'),
                        TextInput::make('description')
                            ->label('Description')
                            ->helperText('What this variable represents'),
                    ])
                    ->collapsible()
                    ->itemLabel(fn(array $state): ?string => $state['name'] ?? null),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }
}
