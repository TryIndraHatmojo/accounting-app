<?php

namespace App\Filament\Resources\ShipmentNotices\Pages;

use App\Filament\Resources\ShipmentNotices\ShipmentNoticeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateShipmentNotice extends CreateRecord
{
    protected static string $resource = ShipmentNoticeResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['recorded_by'] = auth()->id();

        return $data;
    }
}
