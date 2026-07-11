<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->disabled(fn (): bool => $this->record->users()->exists())
                ->tooltip(fn (): ?string => $this->record->users()->exists()
                    ? 'Role yang masih digunakan tidak dapat dihapus.'
                    : null),
        ];
    }
}
