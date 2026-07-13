<?php

namespace App\Filament\Resources\ExportDeclarations\Schemas;

use App\Actions\StoreOptimizedExportAttachment;
use Filament\Facades\Filament;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ExportDeclarationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pemberitahuan Ekspor Barang')
                    ->schema([
                        DatePicker::make('document_date')
                            ->label('Tanggal')
                            ->default(today())
                            ->required()
                            ->native(false),
                        TextInput::make('exporter_name')
                            ->label('Nama Eksportir')
                            ->placeholder('Contoh: CV. UNIVERSAL VINDO COCO')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('peb_number')
                            ->label('No. PEB')
                            ->placeholder('Contoh: 41.UVC.06-26')
                            ->required()
                            ->maxLength(255)
                            ->scopedUnique(ignoreRecord: true),
                        TextInput::make('invoice_number')
                            ->label('No. Invoice')
                            ->placeholder('Contoh: 032/LAC/RSS-INV/05/26')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('container_quantity')
                            ->label('Jumlah Kontainer')
                            ->default(1)
                            ->required()
                            ->integer()
                            ->minValue(1),
                        Select::make('container_size')
                            ->label('Ukuran Kontainer')
                            ->options([
                                '20 ft' => "20 ft",
                                '20 ft Refer' => "20 ft Reefer",
                                '20 ft HC' => "20 ft HC",
                                '40 ft' => "40 ft",
                                '40 ft Refer' => "40 ft Reefer",
                                '40 ft HC' => "40 ft HC",
                                '45 ft' => "45 ft",
                                '48 ft' => "48 ft",
                                '53 ft' => "53 ft",
                            ])
                            ->default('20 ft')
                            ->required()
                            ->native(false),
                        TextInput::make('destination_port')
                            ->label('Pelabuhan / Negara Tujuan')
                            ->placeholder('Contoh: India')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(2),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Break Down Container')
                    ->schema([
                        Repeater::make('items')
                            ->label('Rincian Kontainer')
                            ->relationship()
                            ->schema([
                                TextInput::make('container_number')
                                    ->label('Nomor Kontainer')
                                    ->placeholder('Contoh: SIKU 307838 5')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(2),
                                TextInput::make('seal_number')
                                    ->label('Nomor Seal')
                                    ->placeholder('Contoh: 0087588')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('warehouse')
                                    ->label('Gudang')
                                    ->placeholder('Contoh: UVC00059')
                                    ->maxLength(255),
                                Select::make('container_size')
                                    ->label('Ukuran')
                                    ->options([
                                        '20 ft' => "20 ft",
                                        '20 ft Refer' => "20 ft Reefer",
                                        '20 ft HC' => "20 ft HC",
                                        '40 ft' => "40 ft",
                                        '40 ft Refer' => "40 ft Reefer",
                                        '40 ft HC' => "40 ft HC",
                                        '45 ft' => "45 ft",
                                        '48 ft' => "48 ft",
                                        '53 ft' => "53 ft",
                                    ])
                                    ->default('20 ft')
                                    ->required()
                                    ->native(false),
                                TextInput::make('description')
                                    ->label('Deskripsi Barang')
                                    ->placeholder('Contoh: Sticklac')
                                    ->required()
                                    ->maxLength(255),
                                self::weightInput('gross_weight', 'Berat Bruto (Kg)'),
                                self::weightInput('net_weight', 'Berat Netto (Kg)'),
                                TextInput::make('bag_count')
                                    ->label('Jumlah Karung')
                                    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                    ->stripCharacters('.')
                                    ->required()
                                    ->integer()
                                    ->minValue(1),
                            ])
                            ->columns(4)
                            ->defaultItems(1)
                            ->minItems(1)
                            ->itemNumbers()
                            ->cloneable()
                            ->orderColumn('sort_order')
                            ->addActionLabel('Tambah Kontainer')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make('Lampiran Foto')
                    ->description('Foto akan diperkecil maksimal 2.000 px dan dikompres ke WebP kualitas tinggi. Progress tampil selama upload dan penyimpanan.')
                    ->schema([
                        FileUpload::make('attachments')
                            ->label('Foto Lampiran')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(12 * 1024)
                            ->multiple()
                            ->maxFiles(20)
                            ->maxParallelUploads(3)
                            ->loadingIndicatorPosition('right')
                            ->uploadProgressIndicatorPosition('right')
                            ->appendFiles()
                            ->reorderable()
                            ->openable()
                            ->downloadable()
                            ->disk('public')
                            ->visibility('public')
                            ->directory(fn (): string => 'export-declarations/'.Filament::getTenant()->getKey())
                            ->orientImagesFromExif()
                            ->automaticallyResizeImagesMode('contain')
                            ->automaticallyResizeImagesToWidth('2000')
                            ->automaticallyResizeImagesToHeight('2000')
                            ->automaticallyUpscaleImagesWhenResizing(false)
                            ->saveUploadedFileUsing(fn (
                                BaseFileUpload $component,
                                TemporaryUploadedFile $file,
                                StoreOptimizedExportAttachment $storeOptimizedExportAttachment,
                            ): string => $storeOptimizedExportAttachment->handle(
                                $file,
                                $component->getDirectory() ?? 'export-declarations',
                                $component->getDiskName(),
                            ))
                            ->helperText('Format JPG, PNG, atau WebP. Maksimal 12 MB per foto dan 20 foto.')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    private static function weightInput(string $name, string $label): TextInput
    {
        return TextInput::make($name)
            ->label($label)
            ->placeholder('Contoh: 14.740,0')
            ->mask(RawJs::make('$money($input, \',\', \'.\', 3)'))
            ->stripCharacters('.')
            ->required()
            ->rules(['regex:/^\d+(?:,\d{1,3})?$/'])
            ->formatStateUsing(fn (mixed $state): ?string => filled($state)
                ? number_format((float) $state, 3, ',', '.')
                : null)
            ->dehydrateStateUsing(fn (mixed $state): ?string => filled($state)
                ? str_replace(',', '.', str_replace('.', '', (string) $state))
                : null);
    }
}
