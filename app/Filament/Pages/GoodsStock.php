<?php

namespace App\Filament\Pages;

use App\Filament\Resources\GoodsReceipts\GoodsReceiptResource;
use App\Models\Company;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\Product;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use UnitEnum;

class GoodsStock extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static string|UnitEnum|null $navigationGroup = 'Logistik';

    protected static ?string $navigationLabel = 'Stock Barang';

    protected static ?string $title = 'Stock Barang';

    protected static ?string $slug = 'stock-barang';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.goods-stock';

    public function table(Table $table): Table
    {
        $company = $this->currentCompany();

        return $table
            ->query(
                GoodsReceiptItem::query()
                    ->select([
                        'id',
                        'goods_receipt_id',
                        'product_id',
                        'package_count',
                        'final_weight',
                    ])
                    ->with([
                        'goodsReceipt:id,received_date',
                        'product:id,name,abbreviation',
                    ])
                    ->whereIn(
                        'goods_receipt_id',
                        GoodsReceipt::query()
                            ->select('id')
                            ->whereBelongsTo($company),
                    ),
            )
            ->columns([
                TextColumn::make('stock_code')
                    ->label('Kode Barang')
                    ->state(fn (GoodsReceiptItem $record): string => $record->stockCode())
                    ->badge()
                    ->copyable(),
                TextColumn::make('package_count')
                    ->label('Jumlah Koli')
                    ->numeric(locale: 'id')
                    ->alignEnd()
                    ->summarize(
                        Sum::make()
                            ->label('Total Koli')
                            ->numeric(decimalPlaces: 0, locale: 'id'),
                    ),
                TextColumn::make('final_weight')
                    ->label('Total Berat')
                    ->numeric(decimalPlaces: 3, locale: 'id')
                    ->suffix(' kg')
                    ->alignEnd()
                    ->summarize(
                        Sum::make()
                            ->label('Total Berat')
                            ->numeric(decimalPlaces: 3, locale: 'id')
                            ->suffix(' kg'),
                    ),
            ])
            ->filters([
                SelectFilter::make('product_id')
                    ->label('Produk')
                    ->options(fn (): array => Product::query()
                        ->whereBelongsTo($company)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->preload(),
                Filter::make('received_date')
                    ->label('Periode Tanggal Masuk')
                    ->schema([
                        DatePicker::make('from')->label('Dari tanggal'),
                        DatePicker::make('until')->label('Sampai tanggal'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->whereIn(
                        'goods_receipt_id',
                        GoodsReceipt::query()
                            ->select('id')
                            ->whereBelongsTo($company)
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, string $date): Builder => $query->whereDate('received_date', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, string $date): Builder => $query->whereDate('received_date', '<=', $date),
                            ),
                    )),
            ])
            ->groups([
                Group::make('product.name')
                    ->label('Produk')
                    ->titlePrefixedWithLabel(false)
                    ->getTitleFromRecordUsing(
                        fn (GoodsReceiptItem $record): string => Str::upper($record->product->name),
                    )
                    ->collapsible(),
            ])
            ->defaultGroup('product.name')
            ->groupingSettingsHidden()
            ->defaultSort('id', 'desc')
            ->paginated([25, 50, 100, 'all'])
            ->striped()
            ->recordUrl(fn (GoodsReceiptItem $record): string => GoodsReceiptResource::getUrl('view', [
                'record' => $record->goods_receipt_id,
            ]))
            ->emptyStateHeading('Belum ada stok barang')
            ->emptyStateDescription('Stok akan tampil setelah Laporan Penerimaan Barang disimpan.');
    }

    private function currentCompany(): Company
    {
        $tenant = Filament::getTenant();

        abort_unless($tenant instanceof Company, 404);

        return $tenant;
    }
}
