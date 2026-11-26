<?php

namespace App\Filament\Widgets;

use App\Enums\VisitStatus;
use App\Models\VisitRecord;
use Filament\Widgets\ChartWidget;

class VisitStatusChart extends ChartWidget
{
    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && $user->hasAnyRole(['super_admin', 'general_maneger', 'directorate_manager']);
    }

    public function getHeading(): ?string
    {
        return __('Transactions by Status');
    }

    protected function getData(): array
    {
        $user = auth()->user();
        $isDirectorateManager = $user->hasRole('directorate_manager')
            && ! $user->hasAnyRole(['super_admin', 'general_maneger']);

        $labels = [];
        $data = [];
        $colors = [
            VisitStatus::PENDING->value => '#64748b',
            VisitStatus::IN_PROGRESS->value => '#0ea5e9',
            VisitStatus::COMPLETED->value => '#16a34a',
            VisitStatus::ON_HOLD->value => '#f59e0b',
            VisitStatus::TRANSFERRED->value => '#005a36',
            VisitStatus::REJECTED->value => '#dc2626',
        ];
        $bgColors = [];

        foreach (VisitStatus::cases() as $status) {
            $query = VisitRecord::where('current_status', $status->value);

            if ($isDirectorateManager) {
                $query->where('directorate_id', $user->directorate_id);
            }

            $labels[] = $status->getLabel();
            $data[] = $query->count();
            $bgColors[] = $colors[$status->value] ?? '#94a3b8';
        }

        return [
            'datasets' => [
                [
                    'label' => __('Transactions'),
                    'data' => $data,
                    'backgroundColor' => $bgColors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
