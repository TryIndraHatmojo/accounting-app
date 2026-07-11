<?php

namespace App\Filament\Resources\ExpenseTypes\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExpenseTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Jenis pengeluaran')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('expenses_count')
                    ->label('Jumlah transaksi')
                    ->counts('expenses')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
