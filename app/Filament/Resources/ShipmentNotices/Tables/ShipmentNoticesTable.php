<?php

namespace App\Filament\Resources\ShipmentNotices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShipmentNoticesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('document_number')
                    ->label('No. PPB')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('loading_date')
                    ->label('Tanggal Muat')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('origin')
                    ->label('Asal')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('departure_date')
                    ->label('Keberangkatan')
                    ->date('d M Y')
                    ->placeholder('-')
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
            ])
            ->defaultSort('loading_date', 'desc')
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
