<?php

namespace App\Filament\Widgets;

use App\Models\Department;
use App\Models\Directorate;
use App\Models\VisitRecord;
use Filament\Widgets\ChartWidget;

class VisitsDistributionChart extends ChartWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && $user->hasAnyRole(['super_admin', 'general_maneger', 'directorate_manager']);
    }

    public function getHeading(): ?string
    {
        $user = auth()->user();
        $isDirectorateManager = $user && $user->hasRole('directorate_manager')
            && ! $user->hasAnyRole(['super_admin', 'general_maneger']);

        return $isDirectorateManager
            ? __('Visits by Department (Your Directorate)')
            : __('Visits by Directorate');
    }

    protected function getData(): array
    {
        $user = auth()->user();
        $isDirectorateManager = $user->hasRole('directorate_manager')
            && ! $user->hasAnyRole(['super_admin', 'general_maneger']);

        $labels = [];
        $data = [];

        if ($isDirectorateManager) {
            $departments = Department::where('directorate_id', $user->directorate_id)->get();

            foreach ($departments as $department) {
                $labels[] = $department->name;
                $data[] = VisitRecord::whereHas('room', fn($q) => $q->where('department_id', $department->id))->count();
            }
        } else {
            $directorates = Directorate::withCount('visitRecords')->get();

            foreach ($directorates as $directorate) {
                $labels[] = $directorate->name;
                $data[] = $directorate->visit_records_count;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => __('Visits Count'),
                    'data' => $data,
                    'backgroundColor' => '#005a36',
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
