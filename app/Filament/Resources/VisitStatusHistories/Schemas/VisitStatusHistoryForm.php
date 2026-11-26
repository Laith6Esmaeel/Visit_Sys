<?php

namespace App\Filament\Resources\VisitStatusHistories\Schemas;

use App\Enums\VisitStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class VisitStatusHistoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('visit_record_id')
                    ->relationship('visitRecord', 'id')
                    ->required(),
                Select::make('employee_id')
                    ->relationship('employee', 'name')
                    ->required(),
                Select::make('status')
                    ->options(VisitStatus::class)
                    ->required(),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
                DateTimePicker::make('changed_at')
                    ->required(),
            ]);
    }
}
