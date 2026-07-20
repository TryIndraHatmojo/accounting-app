<?php

namespace Tests\Feature;

use App\Filament\Pages\GoodsStock;
use App\Filament\Resources\Companies\CompanyResource;
use App\Filament\Resources\Expenses\ExpenseResource;
use App\Filament\Resources\ExpenseTypes\ExpenseTypeResource;
use App\Filament\Resources\ExportDeclarations\ExportDeclarationResource;
use App\Filament\Resources\GoodsReceipts\GoodsReceiptResource;
use App\Filament\Resources\Incomes\IncomeResource;
use App\Filament\Resources\IncomeTypes\IncomeTypeResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Resources\ShipmentNotices\ShipmentNoticeResource;
use App\Filament\Resources\Suppliers\SupplierResource;
use App\Filament\Resources\Users\UserResource;
use Filament\Support\Icons\Heroicon;
use Tests\TestCase;

class AdminNavigationIconTest extends TestCase
{
    public function test_each_sidebar_item_has_a_representative_icon(): void
    {
        $navigationIcons = [
            CompanyResource::class => [Heroicon::OutlinedBuildingOffice2, Heroicon::BuildingOffice2],
            ExpenseTypeResource::class => [Heroicon::OutlinedTag, Heroicon::Tag],
            UserResource::class => [Heroicon::OutlinedUserGroup, Heroicon::UserGroup],
            ExportDeclarationResource::class => [Heroicon::OutlinedDocumentArrowUp, Heroicon::DocumentArrowUp],
            ExpenseResource::class => [Heroicon::OutlinedArrowTrendingDown, Heroicon::ArrowTrendingDown],
            SupplierResource::class => [Heroicon::OutlinedBuildingStorefront, Heroicon::BuildingStorefront],
            RoleResource::class => [Heroicon::OutlinedShieldCheck, Heroicon::ShieldCheck],
            GoodsReceiptResource::class => [Heroicon::OutlinedInboxArrowDown, Heroicon::InboxArrowDown],
            IncomeTypeResource::class => [Heroicon::OutlinedListBullet, Heroicon::ListBullet],
            IncomeResource::class => [Heroicon::OutlinedBanknotes, Heroicon::Banknotes],
            ProductResource::class => [Heroicon::OutlinedCube, Heroicon::Cube],
            ShipmentNoticeResource::class => [Heroicon::OutlinedTruck, Heroicon::Truck],
            GoodsStock::class => [Heroicon::OutlinedArchiveBox, Heroicon::ArchiveBox],
        ];

        foreach ($navigationIcons as $navigationItem => [$icon, $activeIcon]) {
            $this->assertSame($icon, $navigationItem::getNavigationIcon());
            $this->assertSame($activeIcon, $navigationItem::getActiveNavigationIcon());
        }
    }
}
