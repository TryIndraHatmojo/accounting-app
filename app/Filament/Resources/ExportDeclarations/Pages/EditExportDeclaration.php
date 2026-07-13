<?php

namespace App\Filament\Resources\ExportDeclarations\Pages;

use App\Filament\Resources\ExportDeclarations\ExportDeclarationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditExportDeclaration extends EditRecord
{
    protected static string $resource = ExportDeclarationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
