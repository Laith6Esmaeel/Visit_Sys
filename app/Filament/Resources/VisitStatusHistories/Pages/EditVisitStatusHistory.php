<?php

namespace App\Filament\Resources\VisitStatusHistories\Pages;

use App\Filament\Resources\VisitStatusHistoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVisitStatusHistory extends EditRecord
{
    protected static string $resource = VisitStatusHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
