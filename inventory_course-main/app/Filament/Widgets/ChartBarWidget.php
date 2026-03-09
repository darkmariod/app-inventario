<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;

class ChartBarWidget extends ChartWidget
{
    protected ?string $heading = 'Top 10 Products with the Most Stock';
    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $topProducts = Product::orderBy('stock', 'desc')->take(10)->get();

        return [
            'datasets' => [
                [
                    'label' => 'Current Stock',
                    'data' => $topProducts->pluck('stock')->toArray(),
                    'backgroundColor' => '#673ab7'
                ]
            ],
            'labels' => $topProducts->pluck('name')->toArray()
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'ticks' => [
                        'stepSize' => 1
                    ]
                ]
            ]
        ];
    }

}
