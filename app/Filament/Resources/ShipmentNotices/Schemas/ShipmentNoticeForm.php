<?php

namespace App\Filament\Resources\ShipmentNotices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class ShipmentNoticeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pemberitahuan Pengiriman Barang')
                    ->schema([
                        TextInput::make('document_number')
                            ->label('No. PPB')
                            ->placeholder('Contoh: SINDO-06')
                            ->required()
                            ->maxLength(255)
                            ->scopedUnique(ignoreRecord: true),
                        DatePicker::make('loading_date')
                            ->label('Tanggal Muat')
                            ->default(today())
                            ->required()
                            ->native(false),
                        TextInput::make('origin')
                            ->label('Asal')
                            ->placeholder('Contoh: Kupang, NTT')
                            ->required()
                            ->maxLength(255),
                        DatePicker::make('departure_date')
                            ->label('Tanggal Keberangkatan')
                            ->native(false),
                        TextInput::make('vehicle_number')
                            ->label('No. Kendaraan')
                            ->maxLength(255),
                        Textarea::make('container_numbers')
                            ->label('No. Kontener')
                            ->helperText('Isi satu nomor kontener per baris.')
                            ->rows(2),
                        Textarea::make('seal_numbers')
                            ->label('No. Seal')
                            ->helperText('Isi satu nomor seal per baris.')
                            ->rows(2),
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Deskripsi Barang')
                    ->schema([
                        Repeater::make('items')
                            ->label('Barang yang dikirim')
                            ->relationship()
                            ->schema([
                                TextInput::make('section_name')
                                    ->label('Kelompok pengiriman')
                                    ->placeholder('Contoh: Kontener 1 - SIPU 777134 4')
                                    ->maxLength(255)
                                    ->columnSpan(2),
                                Select::make('product_id')
                                    ->label('Nama Barang')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label('Nama produk')
                                            ->required()
                                            ->maxLength(255)
                                            ->scopedUnique(),
                                        TextInput::make('abbreviation')
                                            ->label('Nama Produk Disingkat')
                                            ->helperText('Contoh: KYT untuk Kunyit.')
                                            ->required()
                                            ->maxLength(20)
                                            ->rules(['regex:/^[A-Za-z0-9]+$/'])
                                            ->scopedUnique(),
                                    ])
                                    ->createOptionModalHeading('Tambah Produk')
                                    ->required()
                                    ->columnSpan(2),
                                TextInput::make('package_count')
                                    ->label('Jumlah Koli')
                                    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                    ->stripCharacters('.')
                                    ->integer()
                                    ->minValue(0),
                                self::weightInput('initial_weight', 'Berat Awal (Kg)'),
                                self::weightInput('final_weight', 'Berat Akhir (Kg)'),
                                TextInput::make('shrinkage_weight')
                                    ->label('Susut (Kg)')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('Dihitung otomatis setelah disimpan.'),
                                TextInput::make('shrinkage_percentage')
                                    ->label('Susut (%)')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('Dihitung otomatis setelah disimpan.'),
                            ])
                            ->columns(4)
                            ->defaultItems(1)
                            ->minItems(1)
                            ->itemNumbers()
                            ->cloneable()
                            ->orderColumn('sort_order')
                            ->addActionLabel('Tambah Barang')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    private static function weightInput(string $name, string $label): TextInput
    {
        return TextInput::make($name)
            ->label($label)
            ->placeholder('Contoh: 7.060,50')
            ->mask(RawJs::make('$money($input, \',\', \'.\', 3)'))
            ->stripCharacters('.')
            ->nullable()
            ->rules(['regex:/^\d+(?:,\d{1,3})?$/'])
            ->formatStateUsing(fn (mixed $state): ?string => filled($state)
                ? number_format((float) $state, 3, ',', '.')
                : null)
            ->dehydrateStateUsing(fn (mixed $state): ?string => filled($state)
                ? str_replace(',', '.', str_replace('.', '', (string) $state))
                : null);
    }
}
