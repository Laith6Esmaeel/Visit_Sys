<?php

namespace App\Filament\Widgets;

use App\Models\VisitRecord;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class VisitsTrendChart extends ChartWidget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && $user->hasAnyRole(['super_admin', 'general_maneger', 'directorate_manager']);
    }

    public function getHeading(): ?string
    {
        return __('Visits Trend (Last 7 Days)');
    }

    protected function getData(): array
    {
        $user = auth()->user();
        $isDirectorateManager = $user->hasRole('directorate_manager')
            && ! $user->hasAnyRole(['super_admin', 'general_maneger']);

        $labels = [];
        $counts = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('m/d');

            $query = VisitRecord::whereDate('check_in', $date);

            if ($isDirectorateManager) {
                $query->where('directorate_id', $user->directorate_id);
            }

            $counts[] = $query->count();
        }

        return [
            'datasets' => [
                [
                    'label' => __('Visits'),
                    'data' => $counts,
                    'borderColor' => '#005a36',
                    'backgroundColor' => 'rgba(0, 90, 54, 0.15)',
                    'fill' => true,
                    'tension' => 0.3,
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
