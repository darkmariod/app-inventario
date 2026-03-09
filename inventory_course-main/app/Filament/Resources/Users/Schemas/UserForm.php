<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->maxLength(100)
                            ->required(),
                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->unique(User::class, ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->dehydrated(fn($state): bool => filled($state))
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->minLength(8)
                            ->helperText('Minimum 8 characters')
                            ->visibleOn('create'),
                    ])
                    ->columns(2),
                Section::make('Roles and Permissions')
                    ->schema([
                        Select::make('roles')
                            ->label('Roles')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Select one or more roles')
                            ->helperText('If you do not select any, the "guest" role will be assigned by default.')
                            ->columnSpanFull(),
                    ])

            ]);
    }
}
