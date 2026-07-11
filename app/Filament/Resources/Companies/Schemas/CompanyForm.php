<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama perusahaan')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label('Kode URL')
                    ->disabled()
                    ->dehydrated(false),
                Textarea::make('address')
                    ->label('Alamat')
                    ->rows(3)
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->required(),
            ]);
    }
}
