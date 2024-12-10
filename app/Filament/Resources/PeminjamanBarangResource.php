<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeminjamanBarangResource\Pages;
use App\Filament\Resources\PeminjamanBarangResource\RelationManagers;
use App\Models\PeminjamanBarang;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PeminjamanBarangResource extends Resource
{
    protected static ?string $model = PeminjamanBarang::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';
    protected static ?string $label = 'Peminjaman';
    protected static ?string $navigationGroup = 'Aktivitas dan Penggunaan';
    protected static ?string $activeNavigationIcon = 'heroicon-s-document-arrow-up';
    protected static ?int $navigationSort = 8;
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() < 4 ? 'danger' : 'info';
    }
    protected static ?string $navigationBadgeTooltip = 'Total Peminjaman';

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

                TextInput::make('nama')
                    ->label('Nama Penanggung Jawab')
                    ->placeholder('Masukkan Nama Penanggung Jawab')
                    ->minLength(3)
                    ->maxLength(45)
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

                DatePicker::make('tanggal_peminjaman')
                    ->label('Tanggal Peminjaman')
                    ->placeholder('Pilih Tanggal Peminjaman')
                    ->native(false)
                    ->required(),

                TextInput::make('kuantitas')
                    ->label('Kuantitas / Banyak')
                    ->placeholder('Masukkan Kuantitas')
                    ->minValue(1)
                    ->suffix(' Kaleng')
                    ->hidden(fn($record) => $record?->status)
                    ->dehydratedWhenHidden()
                    ->numeric()
                    ->required(),

                DatePicker::make('tanggal_pengembalian')
                    ->label('Tanggal Pengembalian')
                    ->placeholder('Pilih Tanggal Pengembalian')
                    ->native(false)
                    ->default(fn($record) => $record && $record->status ? now() : null)
                    ->visible(fn($record) => $record?->status),

                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->placeholder('Masukkan Keterangan')
                    ->rows(3)
                    ->autosize()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal_peminjaman')
                    ->label('Tanggal')
                    ->getStateUsing(function ($record) {
                        $tanggalPeminjaman = \Carbon\Carbon::parse($record->tanggal_peminjaman)->translatedFormat('M d, Y');

                        $tanggalPengembalian = $record->tanggal_pengembalian
                            ? \Carbon\Carbon::parse($record->tanggal_pengembalian)->translatedFormat('M d, Y')
                            : '<span class="text-gray-800 text-sm italic">Belum Dikembalikan</span>';

                        return "
                            <div class='text-sm'>
                                <div class='font-normal text-medium text-gray-500'>&#8226; Peminjaman</div>
                                <div class='text-gray-800 mb-2'>
                                    <span>$tanggalPeminjaman</span>
                                </div>

                                <div class='font-normal text-medium text-gray-500'>&#8226; Pengembalian</div>
                                <div class='text-gray-800'>
                                    <span>$tanggalPengembalian</span>
                                </div>
                            </div>
                        ";
                    })
                    ->html()
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

                TextColumn::make('tanggal_pengembalian')
                    ->label('Tanggal Pengembalian')
                    ->getStateUsing(function ($record) {
                        return $record->tanggal_pengembalian
                            ? \Carbon\Carbon::parse($record->tanggal_pengembalian)->translatedFormat('M d, Y')
                            : '<div class="text-gray-300 italic">Belum Dikembalikan</div>';
                    })
                    ->html()
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

                TextColumn::make('nama')
                    ->label('Penanggung Jawab')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tipeLokasi.nama')
                    ->label('Lokasi')
                    ->sortable()
                    ->formatStateUsing(function (Model $record) {
                        $lokasi = $record->tipeLokasi->lokasi->first();
                        $lokasiNama = $lokasi->nama ?? 'Tidak Ada Lokasi';
                        $tipeLokasiNama = $record->tipeLokasi->nama ?? 'Tidak Ada Tipe Lokasi';

                        return "{$lokasiNama} - {$tipeLokasiNama}";
                    }),

                TextColumn::make('barang.ukuran')
                    ->label('Ukuran / Size')
                    ->suffix(' Liter')
                    ->numeric(),

                TextColumn::make('kuantitas')
                    ->label('Kuantitas / Banyak')
                    ->suffix(' Kaleng')
                    ->numeric()
                    ->sortable(),

                ToggleColumn::make('status')
                    ->label('Sudah Dikembalikan ?')
                    ->offColor('danger')
                    ->onColor('success')
                    ->afterStateUpdated(function ($record, $state) {
                        if ($state) {
                            $record->update([
                                'tanggal_pengembalian' => now(),
                            ]);
                        } else {
                            $record->update([
                                'tanggal_pengembalian' => null,
                            ]);
                        }
                    }),
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
            'index' => Pages\ManagePeminjamanBarangs::route('/'),
        ];
    }
}
