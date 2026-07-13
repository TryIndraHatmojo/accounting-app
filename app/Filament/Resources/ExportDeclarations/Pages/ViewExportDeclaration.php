<?php

namespace App\Filament\Resources\ExportDeclarations\Pages;

use App\Filament\Resources\ExportDeclarations\ExportDeclarationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewExportDeclaration extends ViewRecord
{
    protected static string $resource = ExportDeclarationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
