<?php

namespace App\Filament\Resources\VisitRecords\Schemas;

use App\Enums\VisitStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class VisitRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('visitor_id')
                    ->relationship('visitor', 'id')
                    ->required(),
                Select::make('directorate_id')
                    ->relationship('directorate', 'name')
                    ->required(),
                Select::make('room_id')
                    ->relationship('room', 'id')
                    ->required(),
                TextInput::make('visit_reason')
                    ->required(),
                TextInput::make('badge_number')
                    ->default(null),
                TextInput::make('visitor_type')
                    ->default(null),
                Select::make('current_status')
                    ->options(VisitStatus::class)
                    ->default('pending')
                    ->required(),
                DateTimePicker::make('check_in')
                    ->required(),
                DateTimePicker::make('check_out'),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
