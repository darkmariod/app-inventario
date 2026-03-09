<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Category;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InventoryStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalProducts     = Product::count();
        $lowStockProducts  = Product::whereColumn('stock', '<=', 'min_stock')->count();
        $totalMovements    = StockMovement::whereMonth('created_at', now()->month)->count();
        $totalSuppliers    = Supplier::count();
        $totalCategories   = Category::count();
        $totalStockValue   = Product::selectRaw('SUM(stock * cost) as total')->value('total') ?? 0;

        return [
            Stat::make('Total Productos', $totalProducts)
                ->description('Registrados en el sistema')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            Stat::make('Stock Bajo', $lowStockProducts)
                ->description('Productos bajo stock mínimo')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockProducts > 0 ? 'danger' : 'success'),

            Stat::make('Movimientos (mes)', $totalMovements)
                ->description('Entradas y salidas este mes')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),

            Stat::make('Proveedores', $totalSuppliers)
                ->description($totalCategories . ' categorías activas')
                ->descriptionIcon('heroicon-m-truck')
                ->color('warning'),

            Stat::make('Valor del Inventario', '$' . number_format($totalStockValue, 2))
                ->description('Costo total en stock')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
