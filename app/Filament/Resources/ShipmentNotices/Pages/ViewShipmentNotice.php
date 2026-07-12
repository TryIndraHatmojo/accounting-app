<?php

namespace App\Filament\Resources\ShipmentNotices\Pages;

use App\Filament\Resources\ShipmentNotices\ShipmentNoticeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewShipmentNotice extends ViewRecord
{
    protected static string $resource = ShipmentNoticeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
