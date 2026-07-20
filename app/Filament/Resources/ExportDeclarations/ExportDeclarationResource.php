<?php

namespace App\Filament\Resources\ExportDeclarations;

use App\Filament\Resources\ExportDeclarations\Pages\CreateExportDeclaration;
use App\Filament\Resources\ExportDeclarations\Pages\EditExportDeclaration;
use App\Filament\Resources\ExportDeclarations\Pages\ListExportDeclarations;
use App\Filament\Resources\ExportDeclarations\Pages\ViewExportDeclaration;
use App\Filament\Resources\ExportDeclarations\Schemas\ExportDeclarationForm;
use App\Filament\Resources\ExportDeclarations\Schemas\ExportDeclarationInfolist;
use App\Filament\Resources\ExportDeclarations\Tables\ExportDeclarationsTable;
use App\Models\ExportDeclaration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ExportDeclarationResource extends Resource
{
    protected static ?string $model = ExportDeclaration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentArrowUp;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::DocumentArrowUp;

    protected static string|UnitEnum|null $navigationGroup = 'Logistik';

    protected static ?string $navigationLabel = 'Pemberitahuan Ekspor Barang';

    protected static ?string $modelLabel = 'pemberitahuan ekspor barang';

    protected static ?string $pluralModelLabel = 'pemberitahuan ekspor barang';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'peb_number';

    public static function form(Schema $schema): Schema
    {
        return ExportDeclarationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ExportDeclarationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExportDeclarationsTable::configure($table);
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
            'index' => ListExportDeclarations::route('/'),
            'create' => CreateExportDeclaration::route('/create'),
            'view' => ViewExportDeclaration::route('/{record}'),
            'edit' => EditExportDeclaration::route('/{record}/edit'),
        ];
    }
}
