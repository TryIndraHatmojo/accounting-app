<?php

namespace App\Filament\Resources\ExportDeclarations\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExportDeclarationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pemberitahuan Ekspor Barang')
                    ->schema([
                        TextEntry::make('document_date')->label('Tanggal')->date('d/m/Y'),
                        TextEntry::make('exporter_name')->label('Nama Eksportir'),
                        TextEntry::make('peb_number')->label('No. PEB')->badge(),
                        TextEntry::make('invoice_number')->label('No. Invoice'),
                        TextEntry::make('container_quantity')->label('Jumlah Kontainer')->numeric(locale: 'id'),
                        TextEntry::make('container_size')->label('Ukuran Kontainer'),
                        TextEntry::make('destination_port')->label('Pelabuhan / Negara Tujuan'),
                        TextEntry::make('notes')->label('Catatan')->placeholder('-'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Break Down Container')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('Rincian Kontainer')
                            ->schema([
                                TextEntry::make('container_number')->label('Nomor Kontainer')->badge(),
                                TextEntry::make('seal_number')->label('Nomor Seal'),
                                TextEntry::make('warehouse')->label('Gudang')->placeholder('-'),
                                TextEntry::make('container_size')->label('Ukuran'),
                                TextEntry::make('description')->label('Deskripsi Barang'),
                                TextEntry::make('gross_weight')->label('Berat Bruto')->formatStateUsing(self::formatWeight(...)),
                                TextEntry::make('net_weight')->label('Berat Netto')->formatStateUsing(self::formatWeight(...)),
                                TextEntry::make('bag_count')->label('Jumlah Karung')->numeric(locale: 'id'),
                            ])
                            ->columns(4)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make('Lampiran Foto')
                    ->schema([
                        ImageEntry::make('attachments')
                            ->label('Foto Lampiran')
                            ->disk('public')
                            ->visibility('public')
                            ->imageHeight(180)
                            ->square()
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
            ]);
    }

    private static function formatWeight(mixed $state): string
    {
        return number_format((float) $state, 3, ',', '.').' kg';
    }
}
