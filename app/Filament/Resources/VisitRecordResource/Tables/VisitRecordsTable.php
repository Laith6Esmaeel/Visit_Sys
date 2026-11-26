<?php

namespace App\Filament\Resources\VisitRecordResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VisitRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('visitor.id')
                    ->searchable(),
                TextColumn::make('directorate.name')
                    ->searchable(),
                TextColumn::make('room.id')
                    ->searchable(),
                TextColumn::make('visit_reason')
                    ->searchable(),
                TextColumn::make('badge_number')
                    ->searchable(),
                TextColumn::make('visitor_type')
                    ->searchable(),
                TextColumn::make('current_status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('check_in')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('check_out')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
