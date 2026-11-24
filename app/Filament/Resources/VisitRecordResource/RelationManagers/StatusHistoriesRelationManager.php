<?php

namespace App\Filament\Resources\VisitRecordResource\RelationManagers;

use App\Enums\VisitStatus;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class StatusHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'statusHistories';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Transaction Status History');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('employee_id')
                    ->label(__('Employee'))
                    ->relationship('employee', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('status')
                    ->label(__('New Status'))
                    ->options(VisitStatus::class)
                    ->required(),
                DateTimePicker::make('changed_at')
                    ->label(__('Changed At'))
                    ->default(now())
                    ->required(),
                Textarea::make('notes')
                    ->label(__('Notes'))
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('changed_at', 'desc')
            ->columns([
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge(),
                TextColumn::make('employee.name')
                    ->label(__('Updated By (Employee)'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('notes')
                    ->label(__('Notes'))
                    ->limit(50),
                TextColumn::make('changed_at')
                    ->label(__('Changed At'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('Add Status Update')),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}