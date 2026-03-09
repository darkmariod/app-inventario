<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use Filament\Widgets\ChartWidget;

class ChartPieWidget extends ChartWidget
{
    protected ?string $heading = 'Categories Chart Pie';
    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $categories =  Category::withCount('products')->get();
        $data  = $categories->pluck('products_count')->toArray();
        $labels = $categories->pluck('name')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Quantity of Products',
                    'data' => $data,
                    'backgroundColor' => [
                        '#E91E63',
                        '#9C27B0',
                        '#673AB7',
                        '#3F51B5',
                        '#2196F3',
                        '#03A9F4',
                        '#00BCD4',
                        '#009688',
                        '#4CAF50',
                        '#8BC34A',
                        '#CDDC39',
                        '#FFC107',
                        '#FF9800',
                        '#FF5722',
                    ]
                ]
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'right'
                ]
            ]
        ];
    }
}
