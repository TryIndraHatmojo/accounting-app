<?php

namespace App\Filament\Resources\ExportDeclarations\Pages;

use App\Filament\Resources\ExportDeclarations\ExportDeclarationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExportDeclarations extends ListRecords
{
    protected static string $resource = ExportDeclarationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
