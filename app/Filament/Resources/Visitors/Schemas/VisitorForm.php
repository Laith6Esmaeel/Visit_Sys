<?php

namespace App\Filament\Resources\Visitors\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VisitorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('national_id')
                    ->default(null),
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('last_name')
                    ->required(),
                TextInput::make('father_name')
                    ->default(null),
                TextInput::make('mother_name')
                    ->default(null),
                TextInput::make('address')
                    ->default(null),
                TextInput::make('phone')
                    ->tel()
                    ->default(null),
            ]);
    }
}
