<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ExportDeclarations\ExportDeclarationResource;
use App\Filament\Resources\GoodsReceipts\GoodsReceiptResource;
use App\Models\Company;
use App\Models\ExportDeclaration;
use App\Models\ExportDeclarationItem;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\GoodsStockMovement;
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
use Illuminate\Database\Eloquent\Model;
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
            ->query($this->stockMovementsQuery($company))
            ->columns([
                TextColumn::make('movement_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('stock_code')
                    ->label('Kode Barang')
                    ->state(fn (GoodsStockMovement $record): string => $record->stockCode())
                    ->badge()
                    ->copyable(),
                TextColumn::make('movement_type')
                    ->label('Mutasi')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        GoodsStockMovement::TYPE_INCOMING => 'Barang Masuk',
                        GoodsStockMovement::TYPE_OUTGOING => 'Barang Keluar',
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        GoodsStockMovement::TYPE_INCOMING => 'success',
                        GoodsStockMovement::TYPE_OUTGOING => 'danger',
                    }),
                TextColumn::make('reference_number')
                    ->label('Referensi')
                    ->description(fn (GoodsStockMovement $record): string => $record->isOutgoing() ? 'PEB' : 'LPB')
                    ->searchable()
                    ->badge()
                    ->url(fn (GoodsStockMovement $record): string => $this->referenceUrl($record)),
                TextColumn::make('description')
                    ->label('Keterangan')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('package_change')
                    ->label('Mutasi Koli')
                    ->numeric(locale: 'id')
                    ->alignEnd()
                    ->summarize(
                        Sum::make('stock')
                            ->label('Stok Koli')
                            ->numeric(decimalPlaces: 0, locale: 'id'),
                    ),
                TextColumn::make('weight_change')
                    ->label('Mutasi Berat')
                    ->numeric(decimalPlaces: 3, locale: 'id')
                    ->suffix(' kg')
                    ->alignEnd()
                    ->summarize(
                        Sum::make('stock')
                            ->label('Stok Berat')
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
                Filter::make('movement_date')
                    ->label('Periode Tanggal Mutasi')
                    ->schema([
                        DatePicker::make('from')->label('Dari tanggal'),
                        DatePicker::make('until')->label('Sampai tanggal'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when(
                            $data['from'] ?? null,
                            fn (Builder $query, string $date): Builder => $query->whereDate('movement_date', '>=', $date),
                        )
                        ->when(
                            $data['until'] ?? null,
                            fn (Builder $query, string $date): Builder => $query->whereDate('movement_date', '<=', $date),
                        )),
            ])
            ->groups([
                Group::make('product.name')
                    ->label('Produk')
                    ->titlePrefixedWithLabel(false)
                    ->getTitleFromRecordUsing(
                        fn (GoodsStockMovement $record): string => Str::upper($record->product->name),
                    )
                    ->collapsible(),
            ])
            ->defaultGroup('product.name')
            ->groupingSettingsHidden()
            ->defaultSort('movement_date', 'desc')
            ->paginated([25, 50, 100, 'all'])
            ->striped()
            ->recordUrl(fn (GoodsStockMovement $record): string => $this->referenceUrl($record))
            ->emptyStateHeading('Belum ada stok barang')
            ->emptyStateDescription('Stok akan tampil setelah Laporan Penerimaan Barang atau Pemberitahuan Ekspor Barang disimpan.');
    }

    private function stockMovementsQuery(Company $company): Builder
    {
        $goodsReceiptItemsTable = (new GoodsReceiptItem)->getTable();
        $goodsReceiptsTable = (new GoodsReceipt)->getTable();
        $exportDeclarationItemsTable = (new ExportDeclarationItem)->getTable();
        $exportDeclarationsTable = (new ExportDeclaration)->getTable();

        $incomingMovements = GoodsReceiptItem::query()
            ->join(
                $goodsReceiptsTable,
                $goodsReceiptsTable.'.id',
                '=',
                $goodsReceiptItemsTable.'.goods_receipt_id',
            )
            ->where($goodsReceiptsTable.'.company_id', $company->getKey())
            ->select([
                $goodsReceiptItemsTable.'.id as id',
                $goodsReceiptItemsTable.'.product_id',
                $goodsReceiptsTable.'.id as reference_id',
                $goodsReceiptsTable.'.document_number as reference_number',
                $goodsReceiptsTable.'.received_date as movement_date',
                $goodsReceiptItemsTable.'.section_name as description',
            ])
            ->selectRaw('? as movement_type', [GoodsStockMovement::TYPE_INCOMING])
            ->selectRaw('COALESCE('.$goodsReceiptItemsTable.'.package_count, 0) as package_change')
            ->selectRaw('COALESCE('.$goodsReceiptItemsTable.'.final_weight, 0) as weight_change');

        $outgoingMovements = ExportDeclarationItem::query()
            ->join(
                $exportDeclarationsTable,
                $exportDeclarationsTable.'.id',
                '=',
                $exportDeclarationItemsTable.'.export_declaration_id',
            )
            ->where($exportDeclarationsTable.'.company_id', $company->getKey())
            ->whereNotNull($exportDeclarationItemsTable.'.product_id')
            ->selectRaw('-'.$exportDeclarationItemsTable.'.id as id')
            ->addSelect([
                $exportDeclarationItemsTable.'.product_id',
                $exportDeclarationsTable.'.id as reference_id',
                $exportDeclarationsTable.'.peb_number as reference_number',
                $exportDeclarationsTable.'.document_date as movement_date',
                $exportDeclarationItemsTable.'.description',
            ])
            ->selectRaw('? as movement_type', [GoodsStockMovement::TYPE_OUTGOING])
            ->selectRaw('-'.$exportDeclarationItemsTable.'.bag_count as package_change')
            ->selectRaw('-'.$exportDeclarationItemsTable.'.net_weight as weight_change');

        return GoodsStockMovement::query()
            ->fromSub($incomingMovements->unionAll($outgoingMovements), 'goods_stock_movements')
            ->select('goods_stock_movements.*')
            ->with('product:id,name,abbreviation');
    }

    private function referenceUrl(GoodsStockMovement $record): string
    {
        $resource = $record->isOutgoing()
            ? ExportDeclarationResource::class
            : GoodsReceiptResource::class;

        return $resource::getUrl('view', ['record' => $record->reference_id]);
    }

    protected function resolveTableRecord(?string $key): Model|array|null
    {
        if ($key === null) {
            return null;
        }

        return $this->getTableRecords()->first(
            fn (GoodsStockMovement $record): bool => $this->getTableRecordKey($record) === $key,
        );
    }

    private function currentCompany(): Company
    {
        $tenant = Filament::getTenant();

        abort_unless($tenant instanceof Company, 404);

        return $tenant;
    }
}
