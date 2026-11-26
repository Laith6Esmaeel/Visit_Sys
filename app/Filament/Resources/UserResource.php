<?php

namespace App\Filament\Resources;
use App\Filament\Resources\UserS\Pages;
use App\Models\Employee;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('System Administration');
    }

    public static function getModelLabel(): string
    {
        return __('User');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Users');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        Select::make('employee_id')
                            ->label(__('Employee'))
                            ->relationship('employee', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->unique(ignoreRecord: true)
                            ->live()
                            ->afterStateUpdated(function (?string $state, Set $set) {
                                if (! $state) {
                                    return;
                                }

                                $employee = Employee::with('room.department')->find($state);
                                if ($employee) {
                                    $set('name', $employee->name);
                                    $set('directorate_id', $employee->room?->department?->directorate_id);
                                }
                            })
                            ->helperText(__('Select an employee to link this account to them')),

                        TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label(__('Email'))
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),

                        TextInput::make('password')
                            ->label(__('Password'))
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create'),

                        Select::make('directorate_id')
                            ->label(__('Directorate'))
                            ->relationship('directorate', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Select::make('roles')
                            ->label(__('Roles'))
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
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
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('employee.name')
                    ->label(__('Employee'))
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable(),
                TextColumn::make('directorate.name')
                    ->label(__('Directorate'))
                    ->placeholder('—'),
                TextColumn::make('roles.name')
                    ->label(__('Roles'))
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
// namespace App\Filament\Resources;

// use App\Filament\Resources\UserS\Pages;
// use App\Models\User;
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
// use Filament\Tables\Table;
// use Illuminate\Support\Facades\Hash;

// class UserResource extends Resource
// {
//     protected static ?string $model = User::class;

//     protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

//     protected static ?int $navigationSort = 1;

//     public static function getNavigationGroup(): ?string
//     {
//         return __('System Administration');
//     }

//     public static function getModelLabel(): string
//     {
//         return __('User');
//     }

//     public static function getPluralModelLabel(): string
//     {
//         return __('Users');
//     }

//     public static function form(Schema $schema): Schema
//     {
//         return $schema
//             ->components([
//                 Section::make()
//                     ->columns(2)
//                     ->schema([
//                         TextInput::make('name')
//                             ->label(__('Name'))
//                             ->required()
//                             ->maxLength(255),
//                         TextInput::make('email')
//                             ->label(__('Email'))
//                             ->email()
//                             ->unique(ignoreRecord: true)
//                             ->required()
//                             ->maxLength(255),
//                         TextInput::make('password')
//                             ->label(__('Password'))
//                             ->password()
//                             ->dehydrateStateUsing(fn($state) => Hash::make($state))
//                             ->dehydrated(fn($state) => filled($state))
//                             ->required(fn(string $operation): bool => $operation === 'create'),
//                         Select::make('roles')
//                             ->label(__('Roles'))
//                             ->relationship('roles', 'name')
//                             ->multiple()
//                             ->preload()
//                             ->searchable(),
//                         Select::make('directorate_id')
//                             ->label(__('Directorate'))
//                             ->relationship('directorate', 'name')
//                             ->searchable()
//                             ->preload()
//                             ->nullable()
//                             ->helperText(__('Required only for Directorate Manager')),
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
//                     ->label(__('Name'))
//                     ->searchable()
//                     ->sortable(),
//                 TextColumn::make('email')
//                     ->label(__('Email'))
//                     ->searchable(),
//                 TextColumn::make('roles.name')
//                     ->label(__('Roles'))
//                     ->badge()
//                     ->color('primary'),
//             ])
//             ->filters([])
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
//             'index' => Pages\ListUsers::route('/'),
//             'create' => Pages\CreateUser::route('/create'),
//             'edit' => Pages\EditUser::route('/{record}/edit'),
//         ];
//     }
// }
