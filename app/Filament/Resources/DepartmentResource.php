<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Departments\Pages;
use App\Models\Department;
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

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('Organizational Structure');
    }

    public static function getModelLabel(): string
    {
        return __('Department');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Departments');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user && $user->hasRole('directorate_manager') && ! $user->hasRole('super_admin')) {
            $query->where('directorate_id', $user->directorate_id);
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
                        Select::make('directorate_id')
                            ->label(__('Directorate'))
                            ->relationship('directorate', 'name')
                            ->default(fn () => auth()->user()?->directorate_id)
                            ->disabled(fn () => auth()->user()?->hasRole('directorate_manager') && ! auth()->user()?->hasRole('super_admin'))
                            ->dehydrated()
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('name')
                            ->label(__('Department Name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('floor')
                            ->label(__('Floor'))
                            ->maxLength(100),
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
                    ->label(__('Department Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('directorate.name')
                    ->label(__('Directorate'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('floor')
                    ->label(__('Floor'))
                    ->searchable(),
                TextColumn::make('rooms_count')
                    ->counts('rooms')
                    ->label(__('Rooms Count'))
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                SelectFilter::make('directorate_id')
                    ->label(__('Directorate'))
                    ->relationship('directorate', 'name')
                    ->visible(fn () => auth()->user()?->hasRole('super_admin'))
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
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
}
// namespace App\Filament\Resources;

// use App\Filament\Resources\Departments\Pages;
// use App\Models\Department;
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
// class DepartmentResource extends Resource
// {

//     protected static ?string $model = Department::class;

//     protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

//     protected static ?int $navigationSort = 2;

//     public static function getNavigationGroup(): ?string
//     {
//         return __('Organizational Structure');
//     }

//     public static function getModelLabel(): string
//     {
//         return __('Department');
//     }

//     public static function getPluralModelLabel(): string
//     {
//         return __('Departments');
//     }

//     public static function form(Schema $schema): Schema
//     {
//         return $schema
//             ->components([
//                 Section::make()
//                     ->columns(2)
//                     ->schema([
//                         Select::make('directorate_id')
//                             ->label(__('Directorate'))
//                             ->relationship('directorate', 'name')
//                             ->default(fn() => auth()->user()?->directorate_id)
//                             ->disabled(fn() => auth()->user()?->hasRole('directorate_manager') && ! auth()->user()?->hasRole('super_admin'))
//                             ->dehydrated()
//                             ->required(),
//                         TextInput::make('name')
//                             ->label(__('Department Name'))
//                             ->required()
//                             ->maxLength(255),
//                         TextInput::make('floor')
//                             ->label(__('Floor'))
//                             ->maxLength(100),
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
//                 TextColumn::make('name')
//                     ->label(__('Department Name'))
//                     ->searchable()
//                     ->sortable(),
//                 TextColumn::make('directorate.name')
//                     ->label(__('Directorate'))
//                     ->searchable()
//                     ->sortable(),
//                 TextColumn::make('floor')
//                     ->label(__('Floor'))
//                     ->searchable(),
//                 TextColumn::make('rooms_count')
//                     ->counts('rooms')
//                     ->label(__('Rooms Count'))
//                     ->badge()
//                     ->color('info'),
//             ])
//             ->filters([
//                 SelectFilter::make('directorate_id')
//                     ->label(__('Directorate'))
//                     ->relationship('directorate', 'name')
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
//     public static function getEloquentQuery(): Builder
//     {
//         $query = parent::getEloquentQuery();
//         $user = auth()->user();

//         if ($user && $user->hasRole('directorate_manager') && ! $user->hasRole('super_admin')) {
//             $query->where('id', $user->directorate_id);
//         }

//         return $query;
//     }
//     public static function getPages(): array
//     {
//         return [
//             'index' => Pages\ListDepartments::route('/'),
//             'create' => Pages\CreateDepartment::route('/create'),
//             'edit' => Pages\EditDepartment::route('/{record}/edit'),
//         ];
//     }
// }
