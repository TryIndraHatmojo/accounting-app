<?php

namespace App\Filament\Resources\IncomeTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class IncomeTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama jenis uang masuk')
                    ->required()
                    ->maxLength(255)
                    ->scopedUnique(ignoreRecord: true),
            ]);
    }
}
