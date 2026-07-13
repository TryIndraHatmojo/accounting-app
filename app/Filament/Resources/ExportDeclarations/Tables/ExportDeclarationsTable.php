<?php

namespace App\Filament\Resources\ExportDeclarations\Tables;

use App\Models\ExportDeclaration;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExportDeclarationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('peb_number')
                    ->label('No. PEB')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('invoice_number')
                    ->label('No. Invoice')
                    ->searchable(),
                TextColumn::make('document_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('exporter_name')
                    ->label('Eksportir')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('destination_port')
                    ->label('Tujuan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('items_count')
                    ->label('Kontainer')
                    ->counts('items')
                    ->badge()
                    ->sortable(),
                TextColumn::make('attachments_count')
                    ->label('Lampiran')
                    ->state(fn (ExportDeclaration $record): int => count($record->attachments ?? []))
                    ->badge(),
                TextColumn::make('recorder.name')
                    ->label('Dicatat oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
            ])
            ->defaultSort('document_date', 'desc')
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
