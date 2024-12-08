<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeminjamanBarangResource\Pages;
use App\Filament\Resources\PeminjamanBarangResource\RelationManagers;
use App\Models\PeminjamanBarang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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
                Forms\Components\Select::make('barang_id')
                    ->relationship('barang', 'id')
                    ->required(),
                Forms\Components\Select::make('tipe_lokasi_id')
                    ->relationship('tipeLokasi', 'id')
                    ->required(),
                Forms\Components\DatePicker::make('tanggal_peminjaman')
                    ->required(),
                Forms\Components\DatePicker::make('tanggal_pengembalian'),
                Forms\Components\TextInput::make('kuantitas')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(45),
                Forms\Components\Toggle::make('status')
                    ->required(),
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('barang.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipeLokasi.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_peminjaman')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_pengembalian')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kuantitas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ManagePeminjamanBarangs::route('/'),
        ];
    }
}
