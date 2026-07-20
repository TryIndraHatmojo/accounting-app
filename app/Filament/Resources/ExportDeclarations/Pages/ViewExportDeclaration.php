<?php

namespace App\Filament\Resources\ExportDeclarations\Pages;

use App\Filament\Resources\ExportDeclarations\ExportDeclarationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;

class ViewExportDeclaration extends ViewRecord
{
    protected static string $resource = ExportDeclarationResource::class;

    protected function resolveRecord(int|string $key): Model
    {
        return parent::resolveRecord($key)->load('items.product');
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
