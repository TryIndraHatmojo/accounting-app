<?php

namespace App\Filament\Resources\IncomeTypes\Pages;

use App\Filament\Resources\IncomeTypes\IncomeTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIncomeTypes extends ListRecords
{
    protected static string $resource = IncomeTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
