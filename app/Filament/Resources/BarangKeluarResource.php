<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangKeluarResource\Pages;
use App\Filament\Resources\BarangKeluarResource\RelationManagers;
use App\Models\BarangKeluar;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BarangKeluarResource extends Resource
{
    protected static ?string $model = BarangKeluar::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-on-square';
    protected static ?string $label = 'Material Keluar';
    protected static ?string $navigationGroup = 'Inventori Material';
    protected static ?string $activeNavigationIcon = 'heroicon-s-arrow-up-on-square';
    protected static ?int $navigationSort = 4;
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() < 10 ? 'warning' : 'info';
    }
    protected static ?string $navigationBadgeTooltip = 'Total Material Keluar';

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
                    ->getOptionLabelFromRecordUsing(function (Model $record) {
                        $produkNama = $record->produk->nama ?? 'Produk Tidak Ada';
                        $nama = $record->nama ?? 'Tidak Ada Nama';
                        $jenisNama = $record->jenis->nama ?? 'Tidak Ada Jenis';
                        $warna = $record->warna ?? 'Tidak Ada Warna';
                        $kodeWarna = $record->kode_warna ?? 'Tidak Ada Kode';
                        $ukuran = $record->ukuran ?? '-';

                        return "$nama ($jenisNama) - $produkNama, $warna ($kodeWarna), $ukuran Liter";
                    })
                    ->required(),

                Select::make('karyawan_id')
                    ->label('Pengambil Material')
                    ->placeholder('Pilih Karyawan')
                    ->relationship('karyawan', 'id')
                    ->native(false)
                    ->preload()
                    ->searchable()
                    ->getOptionLabelFromRecordUsing(function (Model $record) {
                        $karyawanNama = $record->nama ?? 'Tidak Ada Karyawan';
                        $karyawanNpk = $record->npk ?? 'Tidak Ada Npk';

                        return "$karyawanNpk - $karyawanNama";
                    })
                    ->required(),

                Select::make('tipe_lokasi_id')
                    ->label('Area')
                    ->relationship('tipeLokasi', 'nama')
                    ->native(false)
                    ->preload()
                    ->searchable()
                    ->getOptionLabelFromRecordUsing(function (Model $record) {
                        $lokasiNama = $record->lokasi->pluck('nama')->join(', ') ?: 'Tidak Ada Lokasi';
                        $tipeLokasiNama = $record->nama;
                        return "{$lokasiNama} - {$tipeLokasiNama}";
                    })
                    ->required(),

                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->placeholder('Pilih Tanggal')
                    ->native(false)
                    ->required(),

                TextInput::make('kuantitas')
                    ->label('Kuantitas / Banyak')
                    ->placeholder('Masukkan Kuantitas')
                    ->minValue(1)
                    ->suffix(' Kaleng')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),

                TextColumn::make('tipeLokasi.id')
                    ->label('Lokasi')
                    ->formatStateUsing(function (Model $record) {
                        $lokasi = $record->tipeLokasi->lokasi->first();
                        $lokasiNama = $lokasi->nama ?? 'Tidak Ada Lokasi';
                        $tipeLokasiNama = $record->tipeLokasi->nama ?? 'Tidak Ada Tipe Lokasi';

                        return "{$lokasiNama} - {$tipeLokasiNama}";
                    })
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

                TextColumn::make('barang.ukuran')
                    ->label('Ukuran / Size')
                    ->suffix(' Liter')
                    ->numeric(),

                TextColumn::make('kuantitas')
                    ->label('Kuantitas / Banyak')
                    ->suffix(' Kaleng')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('karyawan.nama')
                    ->label('Pengambil')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    // Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->icon('heroicon-o-ellipsis-horizontal-circle')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBarangKeluars::route('/'),
        ];
    }
}
