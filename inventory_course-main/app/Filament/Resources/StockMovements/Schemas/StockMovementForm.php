<?php

namespace App\Filament\Resources\StockMovements\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StockMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->label('Product Name')
                    ->required()
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('type')
                    ->options(['in' => 'In', 'out' => 'Out', 'adjust' => 'Adjust'])
                    ->required(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                TextInput::make('reference')
                    ->default(null),
                Select::make('user_id')
                    ->label('User Name')
                    ->relationship('user','name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('notes')
                    ->default(null),
            ]);
    }
}
