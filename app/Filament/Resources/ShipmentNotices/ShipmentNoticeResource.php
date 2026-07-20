<?php

namespace App\Filament\Resources\ShipmentNotices;

use App\Filament\Resources\ShipmentNotices\Pages\CreateShipmentNotice;
use App\Filament\Resources\ShipmentNotices\Pages\EditShipmentNotice;
use App\Filament\Resources\ShipmentNotices\Pages\ListShipmentNotices;
use App\Filament\Resources\ShipmentNotices\Pages\ViewShipmentNotice;
use App\Filament\Resources\ShipmentNotices\Schemas\ShipmentNoticeForm;
use App\Filament\Resources\ShipmentNotices\Schemas\ShipmentNoticeInfolist;
use App\Filament\Resources\ShipmentNotices\Tables\ShipmentNoticesTable;
use App\Models\ShipmentNotice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ShipmentNoticeResource extends Resource
{
    protected static ?string $model = ShipmentNotice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Truck;

    protected static string|UnitEnum|null $navigationGroup = 'Logistik';

    protected static ?string $navigationLabel = 'Pemberitahuan Pengiriman Barang';

    protected static ?string $modelLabel = 'pemberitahuan pengiriman barang';

    protected static ?string $pluralModelLabel = 'pemberitahuan pengiriman barang';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'document_number';

    public static function form(Schema $schema): Schema
    {
        return ShipmentNoticeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ShipmentNoticeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShipmentNoticesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListShipmentNotices::route('/'),
            'create' => CreateShipmentNotice::route('/create'),
            'view' => ViewShipmentNotice::route('/{record}'),
            'edit' => EditShipmentNotice::route('/{record}/edit'),
        ];
    }
}
