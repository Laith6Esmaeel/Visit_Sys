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

    public function processCheckout(string $badgeNumber): void
    {
        $visit = VisitRecord::where('badge_number', trim($badgeNumber))
            ->whereNull('check_out')
            ->latest('check_in')
            ->first();

        if (! $visit) {
            Notification::make()
                ->title(__('No active visit found for this badge'))
                ->danger()
                ->send();

            return;
        }

        $visit->update(['check_out' => now()]);

        Notification::make()
            ->title(__('Visitor checked out successfully'))
            ->body("{$visit->visitor->full_name} — #{$visit->badge_number}")
            ->success()
            ->send();
    }
}