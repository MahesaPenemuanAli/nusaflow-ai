<?php

namespace App\Filament\Widgets;

use App\Models\VisitorLog;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class VisitorChart extends ChartWidget
{
    protected ?string $heading = 'Visitors — Last 7 Days';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $days = collect(range(6, 0))->map(fn ($daysAgo) => Carbon::today()->subDays($daysAgo));

        $labels = $days->map(fn ($date) => $date->format('d M'))->toArray();

        $data = $days->map(function ($date) {
            return VisitorLog::where('visit_date', $date->format('Y-m-d'))
                ->sum('visitor_count');
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total Visitors',
                    'data' => $data,
                    'fill' => true,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
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
