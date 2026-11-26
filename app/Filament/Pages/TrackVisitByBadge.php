<?php

namespace App\Filament\Pages;

use App\Enums\VisitStatus;
use App\Models\VisitRecord;
use App\Models\VisitStatusHistory;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class TrackVisitByBadge extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.track-visit-by-badge';

    public string $badgeNumber = '';

    public ?VisitRecord $visit = null;

    public string $newStatus = '';

    public string $statusNotes = '';

    public static function getNavigationGroup(): ?string
    {
        return __('Visits Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Track Visit by Badge');
    }

    public function getTitle(): string
    {
        return __('Track Visit by Badge');
    }

    /**
     * السماح للموظف والمدير العام فقط بالدخول لهذه الصفحة
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && ($user->hasRole('directorate_manager') || $user->hasRole('employee') || $user->hasRole('super_admin'));
    }

    /**
     * البحث عن الزيارة برقم البادج (يدعم الكتابة اليدوية أو مسح الـ QR)
     */
    public function searchVisit(): void
    {
        $input = trim($this->badgeNumber);

        if (empty($input)) {
            Notification::make()
                ->title(__('Please enter a badge number'))
                ->warning()
                ->send();

            return;
        }

        if (preg_match('/BADGE:([^|]+)/i', $input, $matches)) {
            $input = trim($matches[1]);
            $this->badgeNumber = $input;
        }

        $user = auth()->user();

        $query = VisitRecord::with([
            'visitor',
            'directorate',
            'room.department',
            'statusHistories.employee',
        ])->where('badge_number', $input);

        // تقييد مدير المديرية بزيارات مديريته فقط
        if ($user && $user->hasRole('directorate_manager') && ! $user->hasRole('super_admin')) {
            $query->where('directorate_id', $user->directorate_id);
        }

        $this->visit = $query->latest('check_in')->first();

        if (! $this->visit) {
            $this->newStatus = '';
            $this->statusNotes = '';

            Notification::make()
                ->title(__('No visit found with this badge number'))
                ->danger()
                ->send();

            return;
        }

        $this->newStatus = $this->visit->current_status->value;
        $this->statusNotes = '';
    }

    /**
     * تحديث حالة المعاملة وتسجيلها باسم الموظف المسجل دخوله
     */
    public function updateStatus(): void
    {
        if (! $this->visit) {
            return;
        }

        $employeeId = auth()->user()?->employee_id;

        if (! $employeeId) {
            Notification::make()
                ->title(__('Your user account is not linked to an employee'))
                ->danger()
                ->send();

            return;
        }

        $this->validate([
            'newStatus' => ['required', 'string'],
            'statusNotes' => ['nullable', 'string', 'max:1000'],
        ]);

        // إضافة سجل جديد في التاريخ (سيحدّث current_status تلقائياً عبر booted في الموديل)
        VisitStatusHistory::create([
            'visit_record_id' => $this->visit->id,
            'employee_id' => $employeeId,
            'status' => $this->newStatus,
            'notes' => $this->statusNotes ?: null,
            'changed_at' => now(),
        ]);

        // إعادة تحميل بيانات الزيارة لتحديث العرض فوراً
        $this->visit->refresh();
        $this->visit->load(['visitor', 'directorate', 'room.department', 'statusHistories.employee']);
        $this->statusNotes = '';

        Notification::make()
            ->title(__('Transaction status updated successfully'))
            ->success()
            ->send();
    }

    public function getStatusesProperty(): array
    {
        return VisitStatus::cases();
    }
}