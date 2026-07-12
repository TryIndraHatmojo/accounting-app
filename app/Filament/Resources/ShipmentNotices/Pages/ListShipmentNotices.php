<?php

namespace App\Filament\Resources\ShipmentNotices\Pages;

use App\Filament\Resources\ShipmentNotices\ShipmentNoticeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListShipmentNotices extends ListRecords
{
    protected static string $resource = ShipmentNoticeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
