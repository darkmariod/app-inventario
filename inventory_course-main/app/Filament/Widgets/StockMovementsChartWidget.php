<?php

namespace App\Filament\Widgets;

use App\Models\StockMovement;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class StockMovementsChartWidget extends ChartWidget
{
    protected ?string $heading = 'Movimientos de Stock (últimos 7 días)';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $days   = collect(range(6, 0))->map(fn($i) => now()->subDays($i));
        $labels = $days->map(fn($d) => $d->format('d M'))->toArray();

        $entradas = $days->map(fn($d) =>
            StockMovement::whereDate('created_at', $d)
                ->where('type', 'entrada')
                ->sum('quantity')
        )->toArray();

        $salidas = $days->map(fn($d) =>
            StockMovement::whereDate('created_at', $d)
                ->where('type', 'salida')
                ->sum('quantity')
        )->toArray();

        return [
            'datasets' => [
                [
                    'label'           => 'Entradas',
                    'data'            => $entradas,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.2)',
                    'borderColor'     => 'rgb(34, 197, 94)',
                    'borderWidth'     => 2,
                    'tension'         => 0.4,
                ],
                [
                    'label'           => 'Salidas',
                    'data'            => $salidas,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.2)',
                    'borderColor'     => 'rgb(239, 68, 68)',
                    'borderWidth'     => 2,
                    'tension'         => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
