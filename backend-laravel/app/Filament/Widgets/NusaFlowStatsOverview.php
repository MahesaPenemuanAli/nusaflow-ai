<?php

namespace App\Filament\Widgets;

use App\Models\Destination;
use App\Models\DestinationCategory;
use App\Models\Event;
use App\Models\VisitorLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class NusaFlowStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $todayVisitors = VisitorLog::where('visit_date', Carbon::today()->format('Y-m-d'))
            ->sum('visitor_count');

        return [
            Stat::make('Total Destinations', Destination::count())
                ->description('All registered destinations')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('primary'),

            Stat::make('Active Destinations', Destination::where('is_active', true)->count())
                ->description('Currently active')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Categories', DestinationCategory::count())
                ->description('Destination categories')
                ->descriptionIcon('heroicon-m-tag')
                ->color('info'),

            Stat::make('Total Events', Event::count())
                ->description('Registered events')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning'),

            Stat::make('Visitor Logs', VisitorLog::count())
                ->description('Total log entries')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray'),

            Stat::make('Visitors Today', number_format($todayVisitors))
                ->description(Carbon::today()->format('d M Y'))
                ->descriptionIcon('heroicon-m-user-group')
                ->color($todayVisitors > 0 ? 'success' : 'gray'),
        ];
    }
}
