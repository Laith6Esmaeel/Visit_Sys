<?php

namespace App\Filament\Resources\VisitRecordResource\Pages;

use App\Filament\Resources\VisitRecordResource;
use Filament\Resources\Pages\CreateRecord;



class CreateVisitRecord extends CreateRecord
{
    protected static string $resource = VisitRecordResource::class;

    protected function getRedirectUrl(): string
    {
        return route('visit-records.print-badge', $this->record);
    }
}