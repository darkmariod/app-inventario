<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockWidget extends BaseWidget
{
    protected static ?string $heading = '⚠️ Productos con Stock Bajo';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->whereColumn('stock', '<=', 'min_stock')
                    ->orderBy('stock')
            )
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Producto')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoría')
                    ->badge(),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Stock Actual')
                    ->color('danger')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('min_stock')
                    ->label('Stock Mínimo')
                    ->color('warning'),

                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Proveedor'),
            ])
            ->emptyStateHeading('¡Todo el inventario está en orden!')
            ->emptyStateDescription('No hay productos con stock bajo.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->paginated(false);
    }
}
