<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama supplier')
                    ->required()
                    ->maxLength(255)
                    ->scopedUnique(ignoreRecord: true),
            ]);
    }
}
