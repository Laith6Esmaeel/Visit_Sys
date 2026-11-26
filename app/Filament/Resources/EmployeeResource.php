<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Employees\Pages;
use App\Models\Employee;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('Organizational Structure');
    }

    public static function getModelLabel(): string
    {
        return __('Employee');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Employees');
    }

    /**
     * 1. تقييد الجدول: مدير المديرية يرى فقط الموظفين التابعين لغرف دوائر مديريته
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user && $user->hasRole('directorate_manager') && ! $user->hasRole('super_admin')) {
            $query->whereHas('room.department', function (Builder $q) use ($user) {
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
                        TextInput::make('name')
                            ->label(__('Employee Name'))
                            ->required()
                            ->maxLength(255),

                        /**
                         * 2. تقييد قائمة الغرف في الفورم: تظهر فقط غرف دوائر مديريته
                         */
                        Select::make('room_id')
                            ->label(__('Room'))
                            ->relationship(
                                name: 'room',
                                titleAttribute: 'room_number',
                                modifyQueryUsing: function (Builder $query) {
                                    $user = auth()->user();

                                    if ($user && $user->hasRole('directorate_manager') && ! $user->hasRole('super_admin')) {
                                        $query->whereHas('department', fn (Builder $q) => $q->where('directorate_id', $user->directorate_id));
                                    }

                                    return $query->with('department');
                                }
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->room_number} — {$record->department?->name}")
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('Employee Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('room.department.name')
                    ->label(__('Department'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('room.room_number')
                    ->label(__('Room Number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('room.department.directorate.name')
                    ->label(__('Directorate'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                /**
                 * 3. تقييد فلتر الغرف ليعرض غرف مديريته فقط
                 */
                SelectFilter::make('room_id')
                    ->label(__('Room'))
                    ->relationship(
                        name: 'room',
                        titleAttribute: 'room_number',
                        modifyQueryUsing: function (Builder $query) {
                            $user = auth()->user();

                            if ($user && $user->hasRole('directorate_manager') && ! $user->hasRole('super_admin')) {
                                $query->whereHas('department', fn (Builder $q) => $q->where('directorate_id', $user->directorate_id));
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
            'index' => pages\ListEmployees::route('/'),
            'create' => pages\CreateEmployee::route('/create'),
            'edit' => pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}