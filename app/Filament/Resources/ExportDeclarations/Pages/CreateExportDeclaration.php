<?php

namespace App\Filament\Resources\ExportDeclarations\Pages;

use App\Filament\Resources\ExportDeclarations\ExportDeclarationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExportDeclaration extends CreateRecord
{
    protected static string $resource = ExportDeclarationResource::class;

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
