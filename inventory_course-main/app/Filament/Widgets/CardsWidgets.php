<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Carbon\Carbon;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use function PHPSTORM_META\map;

class CardsWidgets extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $chartData = collect(range(6,0))
            ->map(function ($day) {
                $date = Carbon::today()->subDays($day);
                return Category::whereDate('created_at', $date)->count();
            })
            ->toArray();

        return [
             //forma básica
            /* Stat::make('Categories', Category::query()->count()),
            Stat::make('Products', Product::query()->count()),
            Stat::make('Suppliers', Supplier::query()->count()), */

            Stat::make('Categories', Category::query()->count())
                ->description('Total registered categories')
                ->descriptionIcon('heroicon-m-tag', IconPosition::Before)
                ->chart($chartData)
                ->color('primary'),

            Stat::make('Products', Product::query()->count())
                ->description('Total available products')
                ->descriptionIcon('heroicon-m-squares-2x2', IconPosition::Before)
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('info'),

            Stat::make('Suppliers', Supplier::query()->count())
                ->description('Registered suppliers')
                ->descriptionIcon('heroicon-m-truck', IconPosition::Before)
                ->chart([17, 2, 40, 3, 15, 4, 37]) //chart con lineas hardcodeadas
                ->color('warning'),

        ];
    }
}
