<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama produk')
                    ->required()
                    ->maxLength(255)
                    ->scopedUnique(ignoreRecord: true),
                TextInput::make('abbreviation')
                    ->label('Nama Produk Disingkat')
                    ->helperText('Digunakan sebagai awalan kode barang. Contoh: KYT untuk Kunyit.')
                    ->required()
                    ->maxLength(20)
                    ->rules(['regex:/^[A-Za-z0-9]+$/'])
                    ->scopedUnique(ignoreRecord: true),
            ]);
    }
}
