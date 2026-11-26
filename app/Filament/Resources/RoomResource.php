<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Rooms\Pages;
use App\Models\Room;
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

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('Organizational Structure');
    }

    public static function getModelLabel(): string
    {
        return __('Room');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Rooms');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user && $user->hasRole('directorate_manager') && ! $user->hasRole('super_admin')) {
            $query->whereHas('department', function (Builder $q) use ($user) {
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
                        Select::make('department_id')
                            ->label(__('Department'))
                            ->relationship(
                                name: 'department',
                                titleAttribute: 'name',
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
                        TextInput::make('room_number')
                            ->label(__('Room Number'))
                            ->required()
                            ->maxLength(50),
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
                TextColumn::make('room_number')
                    ->label(__('Room Number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('department.name')
                    ->label(__('Department'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('department.directorate.name')
                    ->label(__('Directorate'))
                    ->sortable(),
                TextColumn::make('employees_count')
                    ->counts('employees')
                    ->label(__('Employees Count'))
                    ->badge()
                    ->color('success'),
            ])
            ->filters([
                SelectFilter::make('department_id')
                    ->label(__('Department'))
                    ->relationship(
                        name: 'department',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query) {
                            $user = auth()->user();

                            if ($user && $user->hasRole('directorate_manager') && ! $user->hasRole('super_admin')) {
                                $query->where('directorate_id', $user->directorate_id);
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
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }
}
// namespace App\Filament\Resources;

// use App\Filament\Resources\Rooms\Pages;
// use App\Models\Room;
// use BackedEnum;
// use Filament\Actions\BulkActionGroup;
// use Filament\Actions\DeleteAction;
// use Filament\Actions\DeleteBulkAction;
// use Filament\Actions\EditAction;
// use Filament\Forms\Components\Select;
// use Filament\Forms\Components\TextInput;
// use Filament\Resources\Resource;
// use Filament\Schemas\Components\Section;
// use Filament\Schemas\Schema;
// use Filament\Tables\Columns\TextColumn;
// use Filament\Tables\Filters\SelectFilter;
// use Filament\Tables\Table;
// use Illuminate\Database\Eloquent\Builder;
// class RoomResource extends Resource
// {
//     protected static ?string $model = Room::class;

//     protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';

//     protected static ?int $navigationSort = 3;

//     public static function getNavigationGroup(): ?string
//     {
//         return __('Organizational Structure');
//     }

//     public static function getModelLabel(): string
//     {
//         return __('Room');
//     }

//     public static function getPluralModelLabel(): string
//     {
//         return __('Rooms');
//     }

//     public static function form(Schema $schema): Schema
//     {
//         return $schema
//             ->components([
//                 Section::make()
//                     ->columns(2)
//                     ->schema([
//                         Select::make('department_id')
//                             ->label(__('Department'))
//                             ->relationship('department', 'name')
//                             ->searchable()
//                             ->preload()
//                             ->required(),
//                         TextInput::make('room_number')
//                             ->label(__('Room Number'))
//                             ->required()
//                             ->maxLength(50),
//                     ]),
//             ]);
//     }

//     public static function table(Table $table): Table
//     {
//         return $table
//             ->columns([
//                 TextColumn::make('id')
//                     ->label('#')
//                     ->sortable(),
//                 TextColumn::make('room_number')
//                     ->label(__('Room Number'))
//                     ->searchable()
//                     ->sortable(),
//                 TextColumn::make('department.name')
//                     ->label(__('Department'))
//                     ->searchable()
//                     ->sortable(),
//                 TextColumn::make('department.directorate.name')
//                     ->label(__('Directorate'))
//                     ->sortable(),
//                 TextColumn::make('employees_count')
//                     ->counts('employees')
//                     ->label(__('Employees Count'))
//                     ->badge()
//                     ->color('success'),
//             ])
//             ->filters([
//                 SelectFilter::make('department_id')
//                     ->label(__('Department'))
//                     ->relationship('department', 'name')
//                     ->searchable()
//                     ->preload(),
//             ])
//             ->recordActions([
//                 EditAction::make(),
//                 DeleteAction::make(),
//             ])
//             ->toolbarActions([
//                 BulkActionGroup::make([
//                     DeleteBulkAction::make(),
//                 ]),
//             ]);
//     }

//     public static function getPages(): array
//     {
//         return [
//             'index' => Pages\ListRooms::route('/'),
//             'create' => Pages\CreateRoom::route('/create'),
//             'edit' => Pages\EditRoom::route('/{record}/edit'),
//         ];
//     }
//     public static function getEloquentQuery(): Builder
// {
//     $query = parent::getEloquentQuery();
//     $user = auth()->user();

//     if ($user && $user->hasRole('directorate_manager') && ! $user->hasRole('super_admin')) {
//         $query->where('id', $user->directorate_id);
//     }

//     return $query;
// }
// }