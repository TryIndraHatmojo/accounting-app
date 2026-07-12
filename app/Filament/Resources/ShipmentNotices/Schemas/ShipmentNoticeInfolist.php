<?php

namespace App\Filament\Resources\ShipmentNotices\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ShipmentNoticeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pemberitahuan Pengiriman Barang')
                    ->schema([
                        TextEntry::make('document_number')->label('No. PPB')->badge(),
                        TextEntry::make('loading_date')->label('Tanggal Muat')->date('d/m/Y'),
                        TextEntry::make('origin')->label('Asal'),
                        TextEntry::make('departure_date')->label('Tanggal Keberangkatan')->date('d/m/Y')->placeholder('-'),
                        TextEntry::make('vehicle_number')->label('No. Kendaraan')->placeholder('-'),
                        TextEntry::make('container_numbers')->label('No. Kontener')->placeholder('-'),
                        TextEntry::make('seal_numbers')->label('No. Seal')->placeholder('-'),
                        TextEntry::make('notes')->label('Catatan')->placeholder('-')->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Deskripsi Barang')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('Barang yang dikirim')
                            ->schema([
                                TextEntry::make('section_name')->label('Kelompok')->placeholder('-')->columnSpan(2),
                                TextEntry::make('product.name')->label('Nama Barang')->columnSpan(2),
                                TextEntry::make('package_count')->label('Jumlah Koli')->numeric(locale: 'id'),
                                TextEntry::make('initial_weight')->label('Berat Awal')->formatStateUsing(self::formatWeight(...)),
                                TextEntry::make('final_weight')->label('Berat Akhir')->formatStateUsing(self::formatWeight(...)),
                                TextEntry::make('shrinkage_weight')->label('Susut')->formatStateUsing(self::formatWeight(...)),
                                TextEntry::make('shrinkage_percentage')
                                    ->label('Susut (%)')
                                    ->formatStateUsing(fn (mixed $state): string => filled($state)
                                        ? number_format((float) $state, 3, ',', '.').' %'
                                        : '-'),
                            ])
                            ->columns(6)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    private static function formatWeight(mixed $state): string
    {
        return filled($state)
            ? number_format((float) $state, 3, ',', '.').' kg'
            : '-';
    }
}
