<?php

namespace App\Filament\Resources\Expenses\Tables;

use App\Models\Expense;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('expense_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('batch_number')
                    ->label('Batch')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('batch_type')
                    ->label('Tipe batch')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        Expense::BATCH_TYPE_LOCAL => Expense::BATCH_TYPES[Expense::BATCH_TYPE_LOCAL],
                        Expense::BATCH_TYPE_EXPORT => Expense::BATCH_TYPES[Expense::BATCH_TYPE_EXPORT],
                        default => '-',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        Expense::BATCH_TYPE_LOCAL => 'info',
                        Expense::BATCH_TYPE_EXPORT => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('expenseType.name')
                    ->label('Jenis pengeluaran')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Keterangan')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('item_code')
                    ->label('Item code')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label('Qty')
                    ->formatStateUsing(fn (?string $state): ?string => filled($state)
                        ? rtrim(rtrim(number_format((float) $state, 3, ',', '.'), '0'), ',')
                        : null)
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('unit_price')
                    ->label('Harga satuan')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('operational_cost')
                    ->label('Operational cost')
                    ->state(fn (Expense $record): ?string => $record->cost_category === Expense::COST_CATEGORY_OPERATIONAL ? $record->amount : null)
                    ->money('IDR', locale: 'id')
                    ->summarize(self::costSummary(Expense::COST_CATEGORY_OPERATIONAL)),
                TextColumn::make('technical_cost')
                    ->label('Technical cost')
                    ->state(fn (Expense $record): ?string => $record->cost_category === Expense::COST_CATEGORY_TECHNICAL ? $record->amount : null)
                    ->money('IDR', locale: 'id')
                    ->summarize(self::costSummary(Expense::COST_CATEGORY_TECHNICAL)),
                TextColumn::make('general_cost')
                    ->label('Biaya umum')
                    ->state(fn (Expense $record): ?string => blank($record->cost_category) ? $record->amount : null)
                    ->money('IDR', locale: 'id')
                    ->summarize(self::costSummary())
                    ->toggleable(),
                TextColumn::make('recorder.name')
                    ->label('Dicatat oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('expense_type_id')
                    ->label('Jenis pengeluaran')
                    ->relationship('expenseType', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('batch_type')
                    ->label('Tipe batch')
                    ->options(Expense::BATCH_TYPES),
                SelectFilter::make('cost_category')
                    ->label('Alokasi biaya')
                    ->options(Expense::COST_CATEGORIES),
                Filter::make('batch_number')
                    ->label('Nomor batch')
                    ->schema([
                        TextInput::make('value')
                            ->label('Nomor batch')
                            ->placeholder('Contoh: 5.1'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'] ?? null,
                        fn (Builder $query, string $batchNumber): Builder => $query->where('batch_number', $batchNumber),
                    )),
                Filter::make('expense_date')
                    ->label('Periode')
                    ->schema([
                        DatePicker::make('from')->label('Dari tanggal'),
                        DatePicker::make('until')->label('Sampai tanggal'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when(
                            $data['from'] ?? null,
                            fn (Builder $query, string $date): Builder => $query->whereDate('expense_date', '>=', $date),
                        )
                        ->when(
                            $data['until'] ?? null,
                            fn (Builder $query, string $date): Builder => $query->whereDate('expense_date', '<=', $date),
                        )),
            ])
            ->groups([
                Group::make('batch_number')
                    ->label('Batch')
                    ->getTitleFromRecordUsing(fn (Expense $record): string => filled($record->batch_number)
                        ? "Batch {$record->batch_number} (".match ($record->batch_type) {
                            Expense::BATCH_TYPE_LOCAL => 'LOCAL',
                            Expense::BATCH_TYPE_EXPORT => 'EXPORT',
                            default => 'TANPA TIPE',
                        }.')'
                        : 'Tanpa batch')
                    ->collapsible(),
            ])
            ->defaultGroup('batch_number')
            ->defaultSort('expense_date', 'desc')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function costSummary(?string $costCategory = null): Summarizer
    {
        return Summarizer::make()
            ->label('Total')
            ->using(fn (QueryBuilder $query): mixed => $query
                ->when(
                    $costCategory,
                    fn (QueryBuilder $query, string $costCategory): QueryBuilder => $query->where('cost_category', $costCategory),
                    fn (QueryBuilder $query): QueryBuilder => $query->whereNull('cost_category'),
                )
                ->sum('amount'))
            ->money('IDR', locale: 'id');
    }
}
