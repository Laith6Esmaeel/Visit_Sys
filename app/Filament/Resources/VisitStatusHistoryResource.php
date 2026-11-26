<?php

namespace App\Filament\Resources;

use App\Enums\VisitStatus;
use App\Filament\Resources\VisitStatusHistories\Pages;
use App\Models\VisitStatusHistory;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VisitStatusHistoryResource extends Resource
{
    protected static ?string $model = VisitStatusHistory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('Visits Management');
    }

    public static function getModelLabel(): string
    {
        return __('Status History');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Transaction Status History');
    }

    /**
     * تقييد السجلات: مدير المديرية يرى فقط سجل تتبع معاملات مديريته
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user && $user->hasRole('directorate_manager') && ! $user->hasRole('super_admin')) {
            $query->whereHas('visitRecord', function (Builder $q) use ($user) {
                $q->where('directorate_id', $user->directorate_id);
            });
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        Select::make('visit_record_id')
                            ->label(__('Badge Number'))
                            ->relationship(
                                name: 'visitRecord',
                                titleAttribute: 'badge_number',
                                modifyQueryUsing: function (Builder $query) {
                                    $user = auth()->user();
                                    if ($user && $user->hasRole('directorate_manager') && ! $user->hasRole('super_admin')) {
                                        $query->where('directorate_id', $user->directorate_id);
                                    }

                                    return $query;
                                }
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('employee_id')
                            ->label(__('Updated By (Employee)'))
                            ->relationship(
                                name: 'employee',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (Builder $query) {
                                    $user = auth()->user();
                                    if ($user && $user->hasRole('directorate_manager') && ! $user->hasRole('super_admin')) {
                                        $query->whereHas('room.department', fn (Builder $q) => $q->where('directorate_id', $user->directorate_id));
                                    }

                                    return $query;
                                }
                            )
                            ->default(fn () => auth()->user()?->employee_id)
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('status')
                            ->label(__('Status'))
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
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('changed_at', 'desc')
            ->columns([
                TextColumn::make('visitRecord.badge_number')
                    ->label(__('Badge Number'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('visitRecord.visitor.full_name')
                    ->label(__('Visitor'))
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('visitRecord.room.room_number')
                    ->label(__('Room Number'))
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge(),
                TextColumn::make('employee.name')
                    ->label(__('Updated By (Employee)'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('notes')
                    ->label(__('Notes'))
                    ->limit(40),
                TextColumn::make('changed_at')
                    ->label(__('Changed At'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(VisitStatus::class),
                SelectFilter::make('employee_id')
                    ->label(__('Employee'))
                    ->relationship(
                        name: 'employee',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query) {
                            $user = auth()->user();
                            if ($user && $user->hasRole('directorate_manager') && ! $user->hasRole('super_admin')) {
                                $query->whereHas('room.department', fn (Builder $q) => $q->where('directorate_id', $user->directorate_id));
                            }

                            return $query;
                        }
                    )
                    ->searchable()
                    ->preload(),
            ])
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
            'index' => Pages\ListVisitStatusHistories::route('/'),
            'create' => Pages\CreateVisitStatusHistory::route('/create'),
            'edit' => Pages\EditVisitStatusHistory::route('/{record}/edit'),
        ];
    }
}