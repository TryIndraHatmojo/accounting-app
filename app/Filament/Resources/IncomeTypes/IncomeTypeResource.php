<?php

namespace App\Filament\Resources\IncomeTypes;

use App\Filament\Resources\IncomeTypes\Pages\CreateIncomeType;
use App\Filament\Resources\IncomeTypes\Pages\EditIncomeType;
use App\Filament\Resources\IncomeTypes\Pages\ListIncomeTypes;
use App\Filament\Resources\IncomeTypes\Schemas\IncomeTypeForm;
use App\Filament\Resources\IncomeTypes\Tables\IncomeTypesTable;
use App\Models\IncomeType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class IncomeTypeResource extends Resource
{
    protected static ?string $model = IncomeType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedListBullet;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::ListBullet;

    protected static string|UnitEnum|null $navigationGroup = 'Data Master';

    protected static ?string $navigationLabel = 'Jenis Uang Masuk';

    protected static ?string $modelLabel = 'jenis uang masuk';

    protected static ?string $pluralModelLabel = 'jenis uang masuk';

    protected static ?int $navigationSort = 7;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return IncomeTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IncomeTypesTable::configure($table);
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
            'index' => ListIncomeTypes::route('/'),
            'create' => CreateIncomeType::route('/create'),
            'edit' => EditIncomeType::route('/{record}/edit'),
        ];
    }
}
