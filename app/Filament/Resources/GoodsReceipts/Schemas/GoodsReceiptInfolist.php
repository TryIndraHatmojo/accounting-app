<?php

namespace App\Filament\Resources\GoodsReceipts\Schemas;

use App\Filament\Resources\ShipmentNotices\ShipmentNoticeResource;
use App\Models\GoodsReceipt;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GoodsReceiptInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Laporan Penerimaan Barang')
                    ->schema([
                        TextEntry::make('document_number')->label('No. LPB')->badge(),
                        TextEntry::make('report_date')->label('Tanggal Pembuatan')->date('d/m/Y'),
                        TextEntry::make('shipmentNotice.document_number')
                            ->label('Referensi PPB')
                            ->color('primary')
                            ->url(fn (GoodsReceipt $record): ?string => $record->shipment_notice_id
                                ? ShipmentNoticeResource::getUrl('view', ['record' => $record->shipment_notice_id])
                                : null)
                            ->placeholder('-'),
                        TextEntry::make('supplier.name')->label('Supplier')->badge(),
                        TextEntry::make('origin_reference')->label('Asal Barang / No. PPB')->placeholder('-'),
                        TextEntry::make('origin')->label('Asal')->placeholder('-'),
                        TextEntry::make('received_date')->label('Tanggal Masuk')->date('d/m/Y'),
                        TextEntry::make('transport_type')->label('Jenis Angkutan')->placeholder('-'),
                        TextEntry::make('vehicle_number')->label('No. Kendaraan')->placeholder('-'),
                        TextEntry::make('container_numbers')->label('No. Kontener')->placeholder('-'),
                        TextEntry::make('seal_numbers')->label('No. Seal')->placeholder('-'),
                        TextEntry::make('notes')->label('Catatan')->placeholder('-')->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Deskripsi Barang Diterima')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('Barang yang diterima')
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
