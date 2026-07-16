<?php

namespace App\Filament\Resources\Expenses\Schemas;

use App\Models\Expense;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pengeluaran')
                    ->schema([
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
                            ->placeholder('Contoh: Bongkar 4x20ft SINDO')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Rincian Batch dan Biaya')
                    ->description('Opsional untuk pengeluaran umum. Isi rincian ini untuk laporan biaya batch LOCAL/EXPORT.')
                    ->schema([
                        TextInput::make('batch_number')
                            ->label('Nomor batch')
                            ->placeholder('Contoh: 5.1')
                            ->maxLength(255)
                            ->requiredWith('batch_type'),
                        Select::make('batch_type')
                            ->label('Tipe batch')
                            ->options(Expense::BATCH_TYPES)
                            ->native(false)
                            ->requiredWith('batch_number'),
                        TextInput::make('item_code')
                            ->label('Item code')
                            ->placeholder('Contoh: MT-271125')
                            ->maxLength(255),
                        Select::make('cost_category')
                            ->label('Alokasi biaya')
                            ->options(Expense::COST_CATEGORIES)
                            ->native(false)
                            ->requiredWith(['quantity', 'unit_price']),
                        TextInput::make('quantity')
                            ->label('Qty')
                            ->numeric()
                            ->minValue(0.001)
                            ->requiredWith('unit_price')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set): void {
                                self::updateAmount($get, $set);
                            }),
                        TextInput::make('unit_price')
                            ->label('Harga satuan')
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->stripCharacters('.')
                            ->numeric()
                            ->minValue(1)
                            ->requiredWith('quantity')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set): void {
                                self::updateAmount($get, $set);
                            }),
                        TextInput::make('amount')
                            ->label('Total biaya')
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->stripCharacters('.')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->dehydrateStateUsing(fn (mixed $state, Get $get): mixed => self::calculatedAmount($get) ?? $state)
                            ->helperText('Otomatis dihitung dari Qty x Harga satuan; tetap dapat diisi langsung untuk pengeluaran umum.'),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                        'xl' => 4,
                    ])
                    ->columnSpanFull(),
            ]);
    }

    private static function updateAmount(Get $get, Set $set): void
    {
        $calculatedAmount = self::calculatedAmount($get);

        if ($calculatedAmount !== null) {
            $set('amount', $calculatedAmount);
        }
    }

    private static function calculatedAmount(Get $get): ?float
    {
        $quantity = $get('quantity');
        $unitPrice = $get('unit_price');

        if (blank($quantity) || blank($unitPrice)) {
            return null;
        }

        return round(self::parseNumericValue($quantity) * self::parseNumericValue($unitPrice), 2);
    }

    private static function parseNumericValue(mixed $value): float
    {
        $value = (string) $value;

        if (str_contains($value, ',')) {
            return (float) str_replace(',', '.', str_replace('.', '', $value));
        }

        if (preg_match('/\.\d{3}$/', $value) === 1) {
            return (float) str_replace('.', '', $value);
        }

        return (float) $value;
    }
}
