<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Visitors\Pages;
use App\Models\Visitor;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VisitorResource extends Resource
{
    protected static ?string $model = Visitor::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('Visits Management');
    }

    public static function getModelLabel(): string
    {
        return __('Visitor');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Visitors');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Personal Information'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('national_id')
                            ->label(__('National ID'))
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        TextInput::make('phone')
                            ->label(__('Phone'))
                            ->tel()
                            ->maxLength(30),
                        TextInput::make('first_name')
                            ->label(__('First Name'))
                            ->required()
                            ->maxLength(100),
                        TextInput::make('last_name')
                            ->label(__('Last Name'))
                            ->required()
                            ->maxLength(100),
                        TextInput::make('father_name')
                            ->label(__('Father Name'))
                            ->maxLength(100),
                        TextInput::make('mother_name')
                            ->label(__('Mother Name'))
                            ->maxLength(100),
                        TextInput::make('address')
                            ->label(__('Address'))
                            ->columnSpanFull()
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('national_id')
                    ->label(__('National ID'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('first_name')
                    ->label(__('First Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('father_name')
                    ->label(__('Father Name'))
                    ->searchable(),
                TextColumn::make('last_name')
                    ->label(__('Last Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->searchable(),
                TextColumn::make('visit_records_count')
                    ->counts('visitRecords')
                    ->label(__('Visits Count'))
                    ->badge()
                    ->color('primary'),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVisitors::route('/'),
            'create' => Pages\CreateVisitor::route('/create'),
            'edit' => Pages\EditVisitor::route('/{record}/edit'),
        ];
    }
}