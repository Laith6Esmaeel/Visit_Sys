<?php

namespace App\Filament\Resources\VisitRecordResource\Pages;

use App\Filament\Resources\VisitRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVisitRecord extends EditRecord
{
    protected static string $resource = VisitRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
