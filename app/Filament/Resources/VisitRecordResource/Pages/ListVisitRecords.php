<?php

namespace App\Filament\Resources\VisitRecordResource\Pages;

use App\Filament\Resources\VisitRecordResource;
use App\Models\VisitRecord;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListVisitRecords extends ListRecords
{
    protected static string $resource = VisitRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function processCheckout(string $scannedData): void
    {
        $scannedData = trim($scannedData);

        // استخراج رقم البادج من نص الـ QR إذا كان بصيغة BADGE:...|ID:...
        if (preg_match('/BADGE:([^|]+)/i', $scannedData, $matches)) {
            $badgeNumber = trim($matches[1]);
        } else {
            $badgeNumber = $scannedData;
        }

        $visit = VisitRecord::where('badge_number', $badgeNumber)
            ->whereNull('check_out')
            ->latest('check_in')
            ->first();

        if (! $visit) {
            Notification::make()
                ->title(__('No active visit found for this badge'))
                ->body($badgeNumber)
                ->danger()
                ->send();

            return;
        }

        $visit->update([
            'check_out' => now(),
        ]);

        Notification::make()
            ->title(__('Visitor checked out successfully'))
            ->body("{$visit->visitor?->full_name} — {$visit->badge_number}")
            ->success()
            ->send();
    }
}
