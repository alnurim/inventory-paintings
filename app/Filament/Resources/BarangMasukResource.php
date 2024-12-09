<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangMasukResource\Pages;
use App\Filament\Resources\BarangMasukResource\RelationManagers;
use App\Models\BarangMasuk;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BarangMasukResource extends Resource
{
    protected static ?string $model = BarangMasuk::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-on-square';
    protected static ?string $label = 'Material Masuk';
    protected static ?string $navigationGroup = 'Inventori Material';
    protected static ?string $activeNavigationIcon = 'heroicon-s-arrow-down-on-square';
    protected static ?int $navigationSort = 3;
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() < 10 ? 'warning' : 'info';
    }
    protected static ?string $navigationBadgeTooltip = 'Total Material Masuk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('barang_id')
                    ->label('Material')
                    ->placeholder('Pilih Material')
                    ->relationship('barang', 'nama')
                    ->native(false)
                    ->preload()
                    ->searchable()
                    ->columnSpanFull()
                    ->required()
                    ->getOptionLabelFromRecordUsing(function (Model $record) {
                        $produkNama = $record->produk->nama ?? 'Produk Tidak Ada';
                        $nama = $record->nama ?? 'Tidak Ada Nama';
                        $jenisNama = $record->jenis->nama ?? 'Tidak Ada Jenis';
                        $warna = $record->warna ?? 'Tidak Ada Warna';
                        $kodeWarna = $record->kode_warna ?? 'Tidak Ada Kode';
                        $ukuran = $record->ukuran ?? '-';

                        return "$nama ($jenisNama) - $produkNama, $warna ($kodeWarna), $ukuran Liter";
                    }),

                DatePicker::make('tanggal')
                    ->label('Tanggal Masuk Material')
                    ->placeholder('Pilih Tanggal Material')
                    ->native(false)
                    ->required(),

                TextInput::make('kuantitas')
                    ->label('Kuantitas / Banyak Material')
                    ->placeholder('Masukkan Kuantitas Material')
                    ->minValue(1)
                    ->maxValue(999)
                    ->suffix('Kaleng')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                TextColumn::make('barang.nama')
                    ->label('Material')
                    ->formatStateUsing(function ($record) {
                        $nama = '<span class="text-sm font-medium text-gray-800">' . e($record->barang->nama) . '</span>';
                        $produkNama = $record->barang->produk->nama ?? 'Tidak Ada Produk';
                        $jenisNama = $record->barang->jenis->nama ?? 'Tidak Ada Jenis';
                        $produkJenis = '<span class="text-sm text-gray-500">' . e($produkNama) . ' &#8226; ' . e($jenisNama) . '</span>';
                        return '<div class="flex flex-col">'
                            . $nama
                            . '<div class="mt-1">' . $produkJenis . '</div>'
                            . '</div>';
                    })
                    ->html()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('barang.produk.nama')
                    ->label('Produk')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('barang.jenis.nama')
                    ->label('Jenis Material')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('barang.warna')
                    ->label('Warna')
                    ->formatStateUsing(function ($record) {
                        if (!$record || !$record->barang->warna) {
                            return '<div class="text-gray-500 italic">Tidak ada data</div>';
                        }

                        $colors = config('colors');
                        $warna = $record->barang->warna;
                        $kodeWarna = $record->barang->kode_warna ?? 'Tidak Ada Kode';
                        $colorHex = $colors[$warna] ?? '#cccccc';
                        $colorStyle = 'background-color:' . $colorHex . '; margin-right: 0.625rem;';

                        return '<div class="flex items-center space-x-2">'
                            . '<div class="w-5 h-5 rounded-lg border border-black dark:border-white" style="' . $colorStyle . '"></div>'
                            . '<span class="text-sm font-medium text-gray-800 dark:text-gray-200">' . e($warna) . ' / ' . e($kodeWarna) . '</span>'
                            . '</div>';
                    })
                    ->html()
                    ->searchable(),

                TextColumn::make('barang.kode_warna')
                    ->label('Kode Warna')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('barang.ukuran')
                    ->label('Ukuran / Size')
                    ->suffix(' Liter')
                    ->numeric(),

                Tables\Columns\TextColumn::make('kuantitas')
                    ->label('Kuantitas / Banyak')
                    ->suffix(' Kaleng')
                    ->numeric(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])->icon('heroicon-o-ellipsis-horizontal-circle')
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBarangMasuks::route('/'),
        ];
    }
}
