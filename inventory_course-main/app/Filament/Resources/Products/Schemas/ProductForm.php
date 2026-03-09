<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sku')
                    ->label('SKU')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('$'),
                TextInput::make('cost')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('$'),
                TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('min_stock')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('category_id')
                    ->label('Category')
                    ->required()
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('supplier_id')
                    ->label('Supplier')
                    ->nullable()
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload(),
                // Para mostrar solo los IDs
                /* TextInput::make('category_id')
                    ->required()
                    ->numeric(),
                TextInput::make('supplier_id')
                    ->numeric()
                    ->default(null), */
            ]);
    }
}
