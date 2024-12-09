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
                        return "$npk | $record->nama";
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
                TextColumn::make('karyawan.id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('peminjaman.id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
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
