<?php

namespace App\Filament\Resources\Enrollments\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Schemas\Schema;

class EnrollmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('guest_email')
                    ->label('Guest Email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                TextInput::make('guest_name')
                    ->label('Guest Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('first_name')
                    ->label('First Name')
                    ->maxLength(255),

                TextInput::make('last_name')
                    ->label('Last Name')
                    ->maxLength(255),

                TextInput::make('phone_number')
                    ->label('Phone Number')
                    ->tel()
                    ->maxLength(255),

                TextInput::make('company_name')
                    ->label('Company Name')
                    ->maxLength(255),

                TextInput::make('country')
                    ->label('Country')
                    ->maxLength(255),

                Textarea::make('street_address')
                    ->label('Street Address')
                    ->rows(3),

                TextInput::make('building_type')
                    ->label('Building Type')
                    ->maxLength(255),

                TextInput::make('town_city')
                    ->label('Town/City')
                    ->maxLength(255),

                TextInput::make('state')
                    ->label('State')
                    ->maxLength(255),

                Textarea::make('order_notes')
                    ->label('Order Notes')
                    ->rows(3),

                Toggle::make('deliver_to_different_address')
                    ->label('Deliver to Different Address')
                    ->default(false),

                Textarea::make('different_address')
                    ->label('Different Address')
                    ->rows(3)
                    ->visible(fn($get) => $get('deliver_to_different_address')),

                TextInput::make('total_amount')
                    ->label('Total Amount')
                    ->numeric()
                    ->prefix('$')
                    ->step(0.01),

                TextInput::make('discounted_amount')
                    ->label('Discounted Amount')
                    ->numeric()
                    ->prefix('$')
                    ->step(0.01),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }
}
