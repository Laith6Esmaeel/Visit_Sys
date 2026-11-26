<?php

namespace App\Filament\Widgets;

use App\Enums\VisitStatus;
use App\Models\Department;
use App\Models\Directorate;
use App\Models\Employee;
use App\Models\Room;
use App\Models\Visitor;
use App\Models\VisitRecord;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SystemStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && $user->hasAnyRole(['super_admin', 'general_maneger', 'directorate_manager']);
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $isDirectorateManager = $user->hasRole('directorate_manager')
            && ! $user->hasAnyRole(['super_admin', 'general_maneger']);

        // 1. إحصائيات مدير المديرية (مقيدة بمديريته فقط)
        if ($isDirectorateManager) {
            $directorateId = $user->directorate_id;

            $departmentsCount = Department::where('directorate_id', $directorateId)->count();
            $roomsCount = Room::whereHas('department', fn($q) => $q->where('directorate_id', $directorateId))->count();
            $employeesCount = Employee::whereHas('room.department', fn($q) => $q->where('directorate_id', $directorateId))->count();

            $totalVisits = VisitRecord::where('directorate_id', $directorateId)->count();
            $todayVisits = VisitRecord::where('directorate_id', $directorateId)->whereDate('check_in', today())->count();
            $completedVisits = VisitRecord::where('directorate_id', $directorateId)
                ->where('current_status', VisitStatus::COMPLETED)
                ->count();

            return [
                Stat::make(__('Departments'), $departmentsCount)
                    ->description(__('In your directorate'))
                    ->icon('heroicon-o-squares-2x2')
                    ->color('primary'),

                Stat::make(__('Rooms'), $roomsCount)
                    ->description(__('In your directorate'))
                    ->icon('heroicon-o-key')
                    ->color('info'),

                Stat::make(__('Employees'), $employeesCount)
                    ->description(__('In your directorate'))
                    ->icon('heroicon-o-identification')
                    ->color('warning'),

                Stat::make(__('Directorate Transactions'), $totalVisits)
                    ->description(__('Today') . ": {$todayVisits} | " . __('Completed') . ": {$completedVisits}")
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('success'),
            ];
        }

        // 2. إحصائيات المدير العام والـ Super Admin (كامل النظام)
        $todayVisits = VisitRecord::whereDate('check_in', today())->count();
        $activeInside = VisitRecord::whereNull('check_out')->count();

        return [
            Stat::make(__('Total Visits'), VisitRecord::count())
                ->description(__('Today') . ": {$todayVisits}")
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary'),

            Stat::make(__('Visitors Inside Now'), $activeInside)
                ->description(__('Active visits without check-out'))
                ->icon('heroicon-o-arrow-right-end-on-rectangle')
                ->color('warning'),

            Stat::make(__('Total Visitors'), Visitor::count())
                ->description(__('Registered visitors'))
                ->icon('heroicon-o-user-group')
                ->color('info'),

            Stat::make(__('Organizational Structure'), Directorate::count() . ' / ' . Department::count())
                ->description(__('Directorates / Departments'))
                ->icon('heroicon-o-building-office-2')
                ->color('success'),

            Stat::make(__('Rooms & Employees'), Room::count() . ' / ' . Employee::count())
                ->description(__('Rooms / Employees'))
                ->icon('heroicon-o-identification')
                ->color('gray'),

            Stat::make(__('Completed Transactions'), VisitRecord::where('current_status', VisitStatus::COMPLETED)->count())
                ->description(__('All completed visits'))
                ->icon('heroicon-o-check-badge')
                ->color('success'),
        ];
    }
}
