<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengambilResource\Pages;
use App\Filament\Resources\PengambilResource\RelationManagers;
use App\Models\Pengambil;
use Filament\Forms;
use Filament\Forms\Components\Select;
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

class PengambilResource extends Resource
{
    protected static ?string $model = Pengambil::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $label = 'Pengambil';
    protected static ?string $navigationGroup = 'Aktivitas dan Penggunaan';
    protected static ?string $activeNavigationIcon = 'heroicon-s-user-circle';
    protected static ?int $navigationSort = 9;
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() < 4 ? 'danger' : 'info';
    }
    protected static ?string $navigationBadgeTooltip = 'Total Pengambil';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('karyawan_id')
                    ->label('Karyawan')
                    ->placeholder('Pilih Karyawan')
                    ->relationship('karyawan', 'nama')
                    ->native(false)
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->preload()
                    ->searchable()
                    ->getOptionLabelFromRecordUsing(function (Model $record) {
                        $npk = $record->npk;
                        $nama = $record->nama;
                        return "$npk - $nama";
                    })
                    ->required(),

                Select::make('peminjaman_id')
                    ->label('Peminjaman')
                    ->placeholder('Pilih Peminjaman')
                    ->relationship('peminjaman', 'nama', function ($query) {
                        $query->where('status', false);
                    })
                    ->native(false)
                    ->preload()
                    ->searchable(['barang.jenis.nama', 'barang.nama'])
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->getOptionLabelFromRecordUsing(function (Model $record) {
                        $lokasi = $record->tipeLokasi->lokasi->first();
                        $lokasiNama = $lokasi->nama ?? 'Tidak Ada Lokasi';
                        $area = $record->tipeLokasi->nama ?? 'Tidak Ada Area';
                        $produkNama = $record->barang->produk->nama ?? 'Produk Tidak Ada';
                        $nama = $record->barang->nama ?? 'Tidak Ada Nama';
                        $jenisNama = $record->barang->jenis->nama ?? 'Tidak Ada Jenis';
                        $warna = $record->barang->warna ?? 'Tidak Ada Warna';
                        $kodeWarna = $record->barang->kode_warna ?? 'Tidak Ada Kode';
                        $kuantitas = $record->kuantitas ?? '-';
                        $ukuran = $record->barang->ukuran ?? '-';

                        return "[$lokasiNama - $area] $nama ($jenisNama) | $produkNama, $warna ($kodeWarna), $ukuran Liter / $kuantitas Kaleng";
                    })
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('karyawan.nama')
                    ->formatStateUsing(function ($record) {
                        $karyawanNama = $record->karyawan->nama ?? 'Tidak Ada Nama';
                        $karyawanNpk = $record->karyawan->npk ?? 'Tidak Ada NPK';
                        return "<div class='flex flex-col'>
                                    <span class='text-sm font-medium text-gray-800'>
                                        $karyawanNama
                                    </span>
                                    <div class='mt-1'>
                                        <span class='text-sm text-gray-500'>
                                            $karyawanNpk
                                        </span>
                                    </div>
                                </div>";
                    })
                    ->html()
                    ->sortable(),

                TextColumn::make('peminjaman.barang.nama')
                    ->label('Material')
                    ->formatStateUsing(function ($record) {
                        $nama = '<span class="text-sm font-medium text-gray-800">' . e($record->peminjaman->barang->nama) . '</span>';
                        $produkNama = $record->peminjaman->barang->produk->nama ?? 'Tidak Ada Produk';
                        $jenisNama = $record->peminjaman->barang->jenis->nama ?? 'Tidak Ada Jenis';
                        $produkJenis = '<span class="text-sm text-gray-500">' . e($produkNama) . ' &#8226; ' . e($jenisNama) . '</span>';
                        return '<div class="flex flex-col">'
                            . $nama
                            . '<div class="mt-1">' . $produkJenis . '</div>'
                            . '</div>';
                    })
                    ->html()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('peminjaman.barang.warna')
                    ->label('Warna')
                    ->formatStateUsing(function ($record) {
                        if (!$record || !$record->peminjaman->barang->warna) {
                            return '<div class="text-gray-500 italic">Tidak ada data</div>';
                        }

                        $colors = config('colors');
                        $warna = $record->peminjaman->barang->warna;
                        $kodeWarna = $record->peminjaman->barang->kode_warna ?? 'Tidak Ada Kode';
                        $colorHex = $colors[$warna] ?? '#cccccc';
                        $colorStyle = 'background-color:' . $colorHex . '; margin-right: 0.625rem;';

                        return '<div class="flex items-center space-x-2">'
                            . '<div class="w-5 h-5 rounded-lg border border-black dark:border-white" style="' . $colorStyle . '"></div>'
                            . '<span class="text-sm font-medium text-gray-800 dark:text-gray-200">' . e($warna) . ' / ' . e($kodeWarna) . '</span>'
                            . '</div>';
                    })
                    ->html()
                    ->searchable(),

                TextColumn::make('peminjaman.tipeLokasi.nama')
                    ->label('Lokasi')
                    ->sortable()
                    ->formatStateUsing(function (Model $record) {
                        $lokasi = $record->peminjaman->tipeLokasi->lokasi->first();
                        $lokasiNama = $lokasi->nama ?? 'Tidak Ada Lokasi';
                        $tipeLokasiNama = $record->peminjaman->tipeLokasi->nama ?? 'Tidak Ada Tipe Lokasi';

                        return "{$lokasiNama} - {$tipeLokasiNama}";
                    }),

                TextColumn::make('peminjaman.barang.ukuran')
                    ->label('Ukuran / Size')
                    ->suffix(' Liter')
                    ->numeric(),

                TextColumn::make('peminjaman.kuantitas')
                    ->label('Kuantitas / Banyak')
                    ->suffix(' Kaleng')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('peminjaman.tanggal_peminjaman')
                    ->label('Tanggal')
                    ->getStateUsing(function ($record) {
                        // Format Tanggal Peminjaman
                        $tanggalPeminjaman = \Carbon\Carbon::parse($record->tanggal_peminjaman)->translatedFormat('M d, Y');

                        // Format Tanggal Pengembalian atau tampilkan "Belum Dikembalikan" jika null
                        $tanggalPengembalian = $record->tanggal_pengembalian
                            ? \Carbon\Carbon::parse($record->tanggal_pengembalian)->translatedFormat('M d, Y')
                            : '<span class="text-gray-500 italic">Belum Dikembalikan</span>';

                        // Gabungkan keduanya dengan HTML yang lebih menarik
                        return "
                            <div class='text-sm'>
                                <div class='font-normal text-medium text-gray-800'>Peminjaman</div>
                                <div class='text-gray-500 mb-2'>
                                    <span>&#8226; $tanggalPeminjaman</span>
                                </div>

                                <div class='font-normal text-medium text-gray-800'>Pengembalian</div>
                                <div class='text-gray-500'>
                                    <span>&#8226; $tanggalPengembalian</span>
                                </div>
                            </div>
                        ";
                    })
                    ->html()
                    ->sortable(),
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
            'index' => Pages\ManagePengambils::route('/'),
        ];
    }
}
