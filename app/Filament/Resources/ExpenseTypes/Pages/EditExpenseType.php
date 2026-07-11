<?php

namespace App\Filament\Resources\ExpenseTypes\Pages;

use App\Filament\Resources\ExpenseTypes\ExpenseTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExpenseType extends EditRecord
{
    protected static string $resource = ExpenseTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->disabled(fn (): bool => $this->record->expenses()->exists())
                ->tooltip(fn (): ?string => $this->record->expenses()->exists()
                    ? 'Jenis yang sudah digunakan tidak dapat dihapus.'
                    : null),
        ];
    }
}
