<?php

namespace App\Filament\Resources;

use App\Enums\VisitStatus;
use App\Filament\Resources\VisitRecordResource\Pages;
use App\Filament\Resources\VisitRecordResource\RelationManagers\StatusHistoriesRelationManager;
use App\Models\Room;
use App\Models\VisitRecord;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VisitRecordResource extends Resource
{
    protected static ?string $model = VisitRecord::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('Visits Management');
    }

    public static function getModelLabel(): string
    {
        return __('Visit Record');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Visit Records');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Visit Details'))
                    ->columns(2)
                    ->schema([
                        Select::make('visitor_id')
                            ->label(__('Visitor'))
                            ->relationship('visitor', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->full_name} ({$record->national_id})")
                            ->searchable(['first_name', 'last_name', 'national_id', 'phone'])
                            ->required()
                            ->createOptionForm([
                                TextInput::make('national_id')
                                    ->label(__('National ID'))
                                    ->unique('visitors', 'national_id'),
                                TextInput::make('first_name')
                                    ->label(__('First Name'))
                                    ->required(),
                                TextInput::make('last_name')
                                    ->label(__('Last Name'))
                                    ->required(),
                                TextInput::make('father_name')
                                    ->label(__('Father Name')),
                                TextInput::make('mother_name')
                                    ->label(__('Mother Name')),
                                TextInput::make('phone')
                                    ->label(__('Phone')),
                                TextInput::make('address')
                                    ->label(__('Address')),
                            ]),

                        Select::make('visitor_type')
                            ->label(__('Visitor Type'))
                            ->options([
                                'citizen' => __('Citizen'),
                                'official' => __('Official'),
                                'contractor' => __('Contractor'),
                                'other' => __('Other'),
                            ])
                            ->default('citizen'),

                        Select::make('directorate_id')
                            ->label(__('Directorate'))
                            ->relationship('directorate', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn(Set $set) => $set('room_id', null))
                            ->required(),

                        Select::make('room_id')
                            ->label(__('Room'))
                            ->options(function (Get $get) {
                                $directorateId = $get('directorate_id');
                                if (! $directorateId) {
                                    return Room::pluck('room_number', 'id');
                                }

                                return Room::whereHas('department', fn($q) => $q->where('directorate_id', $directorateId))
                                    ->pluck('room_number', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('visit_reason')
                            ->label(__('Visit Reason'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('badge_number')
                            ->label(__('Badge Number'))
                            ->maxLength(100),

                        Select::make('current_status')
                            ->label(__('Transaction Status'))
                            ->options(VisitStatus::class)
                            ->default(VisitStatus::PENDING)
                            ->required(),

                        DateTimePicker::make('check_in')
                            ->label(__('Check-in Time'))
                            ->default(fn() => now())
                            ->required(),

                        DateTimePicker::make('check_out')
                            ->label(__('Check-out Time')),

                        Textarea::make('notes')
                            ->label(__('Notes'))
                            ->columnSpanFull()
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('check_in', 'desc')
            ->columns([
                TextColumn::make('badge_number')
                    ->label(__('Badge Number'))
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('visitor.full_name')
                    ->label(__('Visitor'))
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('directorate.name')
                    ->label(__('Directorate'))
                    ->sortable(),
                TextColumn::make('room.room_number')
                    ->label(__('Room Number'))
                    ->sortable(),
                TextColumn::make('visit_reason')
                    ->label(__('Visit Reason'))
                    ->limit(30),
                TextColumn::make('current_status')
                    ->label(__('Transaction Status'))
                    ->badge(),
                TextColumn::make('check_in')
                    ->label(__('Check-in Time'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                TextColumn::make('check_out')
                    ->label(__('Check-out Time'))
                    ->dateTime('Y-m-d H:i')
                    ->placeholder(__('Inside'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('current_status')
                    ->label(__('Transaction Status'))
                    ->options(VisitStatus::class),
                SelectFilter::make('directorate_id')
                    ->label(__('Directorate'))
                    ->relationship('directorate', 'name'),
            ])
            ->recordActions([
                Action::make('checkout')
                    ->label(__('Check-out'))
                    ->icon('heroicon-o-arrow-right-start-on-rectangle')
                    ->color('warning')
                    ->visible(fn(VisitRecord $record): bool => is_null($record->check_out))
                    ->requiresConfirmation()
                    ->action(function (VisitRecord $record) {
                        $record->update(['check_out' => now()]);
                        Notification::make()
                            ->title(__('Visitor checked out successfully'))
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
                Action::make('print_badge')
                    ->label(__('Print Badge'))
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn(VisitRecord $record): string => route('visit-records.print-badge', $record))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            StatusHistoriesRelationManager::class,
        ];
    }

    //n+1
public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
{
    return parent::getEloquentQuery()
        ->with(['visitor', 'directorate', 'room.department']);
}
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVisitRecords::route('/'),
            'create' => Pages\CreateVisitRecord::route('/create'),
            'edit' => Pages\EditVisitRecord::route('/{record}/edit'),
        ];
    }
}
