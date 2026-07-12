<?php

namespace App\Filament\Resources\ShipmentNotices\Pages;

use App\Filament\Resources\ShipmentNotices\ShipmentNoticeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditShipmentNotice extends EditRecord
{
    protected static string $resource = ShipmentNoticeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
