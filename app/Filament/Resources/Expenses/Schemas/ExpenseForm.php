<?php

namespace App\Filament\Resources\Expenses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('expense_date')
                    ->label('Tanggal pengeluaran')
                    ->default(today())
                    ->required()
                    ->native(false),
                Select::make('expense_type_id')
                    ->label('Jenis pengeluaran')
                    ->relationship('expenseType', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nama jenis pengeluaran')
                            ->required()
                            ->maxLength(255)
                            ->scopedUnique(),
                    ])
                    ->createOptionModalHeading('Tambah Jenis Pengeluaran')
                    ->required(),
                TextInput::make('description')
                    ->label('Keterangan')
                    ->placeholder('Contoh: Gaji Budi periode Juli 2026')
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
