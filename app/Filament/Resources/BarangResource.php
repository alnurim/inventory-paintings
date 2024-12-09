<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangResource\Pages;
use App\Filament\Resources\BarangResource\RelationManagers;
use App\Models\Barang;
use Filament\Forms;
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
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $label = 'Material';
    protected static ?string $navigationGroup = 'Inventori Material';
    protected static ?string $activeNavigationIcon = 'heroicon-s-cube';
    protected static ?int $navigationSort = 2;
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() < 10 ? 'warning' : 'info';
    }
    protected static ?string $navigationBadgeTooltip = 'Total Material';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')
                    ->label('Nama Material')
                    ->placeholder('Masukkan Nama Material')
                    ->minLength(3)
                    ->maxLength(45)
                    ->columnSpanFull()
                    ->required(),

                Select::make('produk_id')
                    ->label('Produk Material')
                    ->placeholder('Pilih Produk Material')
                    ->relationship('produk', 'nama')
                    ->native(false)
                    ->preload()
                    ->searchable()
                    ->required(),

                Select::make('jenis_id')
                    ->label('Jenis Material')
                    ->placeholder('Pilih Jenis Material')
                    ->relationship('jenis', 'nama')
                    ->native(false)
                    ->preload()
                    ->searchable()
                    ->required(),

                TextInput::make('warna')
                    ->label('Warna Material')
                    ->placeholder('Masukkan warna Material')
                    ->minLength(3)
                    ->maxLength(45)
                    ->required(),

                TextInput::make('kode_warna')
                    ->label('Kode Warna Material')
                    ->placeholder('Masukkan Kode Warna Material')
                    ->minLength(3)
                    ->maxLength(20)
                    ->required(),

                Select::make('ukuran')
                    ->label('Ukuran / Size (Liter)')
                    ->placeholder('Pilih Ukuran / Size')
                    ->options([
                        '5' => '5 Liter',
                        '10' => '10 Liter',
                        '15' => '15 Liter',
                        '20' => '20 Liter',
                    ])
                    ->native(false)
                    ->preload()
                    ->searchable()
                    ->required(),

                TextInput::make('kuantitas')
                    ->label('Kuantitas / Banyak Material')
                    ->placeholder('Masukkan Kuantitas')
                    ->minValue(1)
                    ->maxValue(999)
                    ->numeric()
                    ->suffix('Kaleng')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Material')
                    ->formatStateUsing(function ($record) {
                        $nama = '<span class="text-sm font-medium text-gray-800">' . e($record->nama) . '</span>';
                        $produkNama = $record->produk->nama ?? 'Tidak Ada Produk';
                        $jenisNama = $record->jenis->nama ?? 'Tidak Ada Jenis';
                        $produkJenis = '<span class="text-sm text-gray-500">' . e($produkNama) . ' &#8226; ' . e($jenisNama) . '</span>';
                        return '<div class="flex flex-col">'
                            . $nama
                            . '<div class="mt-1">' . $produkJenis . '</div>'
                            . '</div>';
                    })
                    ->html()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('produk.nama')
                    ->label('Produk')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('jenis.nama')
                    ->label('Jenis Material')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('warna')
                    ->label('Warna')
                    ->formatStateUsing(function ($record) {
                        if (!$record || !$record->warna) {
                            return '<div class="text-gray-500 italic">Tidak ada data</div>';
                        }
                        $warna = $record->warna;
                        $kodeWarna = $record->kode_warna ?? 'Tidak Ada Kode';
                        $colorStyle = 'background-color:' . strtolower($warna) . '; margin-right: 0.625rem;';

                        return '<div class="flex items-center space-x-2">'
                            . '<div class="w-5 h-5 rounded-lg border border-black dark:border-white" style="' . $colorStyle . '"></div>'
                            . '<span class="text-sm font-medium text-gray-800 dark:text-gray-200">' . e($warna) . ' / ' . e($kodeWarna) . '</span>'
                            . '</div>';
                    })
                    ->html()
                    ->searchable(),

                TextColumn::make('kode_warna')
                    ->label('Kode Warna')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('ukuran')
                    ->label('Ukuran / Size')
                    ->suffix(' Liter')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('kuantitas')
                    ->label('Kuantitas / Banyak')
                    ->suffix(' Kaleng')
                    ->numeric()
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
            'index' => Pages\ManageBarangs::route('/'),
        ];
    }
}
