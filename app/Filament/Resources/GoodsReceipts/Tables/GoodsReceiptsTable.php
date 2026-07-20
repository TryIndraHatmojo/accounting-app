<?php

namespace App\Filament\Resources\GoodsReceipts\Tables;

use App\Filament\Resources\ShipmentNotices\ShipmentNoticeResource;
use App\Models\GoodsReceipt;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GoodsReceiptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('document_number')
                    ->label('No. LPB')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('report_date')
                    ->label('Tanggal Pembuatan')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('shipmentNotice.document_number')
                    ->label('Referensi PPB')
                    ->searchable()
                    ->color('primary')
                    ->url(fn (GoodsReceipt $record): ?string => $record->shipment_notice_id
                        ? ShipmentNoticeResource::getUrl('view', ['record' => $record->shipment_notice_id])
                        : null)
                    ->placeholder('-'),
                TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('received_date')
                    ->label('Tanggal Masuk')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('items_count')
                    ->label('Jumlah Barang')
                    ->counts('items')
                    ->badge()
                    ->sortable(),
                TextColumn::make('recorder.name')
                    ->label('Dicatat oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('supplier_id')
                    ->label('Supplier')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('report_date', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
