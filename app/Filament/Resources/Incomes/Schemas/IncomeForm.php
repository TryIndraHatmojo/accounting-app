<?php

namespace App\Filament\Resources\Incomes\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class IncomeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('income_date')
                    ->label('Tanggal uang masuk')
                    ->default(today())
                    ->required()
                    ->native(false),
                Select::make('income_type_id')
                    ->label('Jenis uang masuk')
                    ->relationship('incomeType', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nama jenis uang masuk')
                            ->required()
                            ->maxLength(255)
                            ->scopedUnique(),
                    ])
                    ->createOptionModalHeading('Tambah Jenis Uang Masuk')
                    ->required(),
                TextInput::make('description')
                    ->label('Keterangan')
                    ->placeholder('Contoh: Pembayaran penjualan kopra')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('amount')
                    ->label('Nominal')
                    ->prefix('Rp')
                    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                    ->stripCharacters('.')
                    ->numeric()
                    ->minValue(1)
                    ->required(),
            ]);
    }
}
