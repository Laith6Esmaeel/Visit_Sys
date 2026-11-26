<?php

namespace App\Filament\Resources\VisitStatusHistories\Pages;

use App\Filament\Resources\VisitStatusHistoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVisitStatusHistories extends ListRecords
{
    protected static string $resource = VisitStatusHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
