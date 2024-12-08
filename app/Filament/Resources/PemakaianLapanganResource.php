<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PemakaianLapanganResource\Pages;
use App\Filament\Resources\PemakaianLapanganResource\RelationManagers;
use App\Models\PemakaianLapangan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PemakaianLapanganResource extends Resource
{
    protected static ?string $model = PemakaianLapangan::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $label = 'Pemakaian Lapangan';
    protected static ?string $navigationGroup = 'Aktivitas dan Penggunaan';
    protected static ?string $activeNavigationIcon = 'heroicon-s-clipboard-document-list';
    protected static ?int $navigationSort = 7;
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() < 4 ? 'danger' : 'info';
    }
    protected static ?string $navigationBadgeTooltip = 'Total Pemakaian Lapangan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('barang_keluar_id')
                    ->relationship('barangKeluar', 'id')
                    ->required(),
                Forms\Components\Select::make('karyawan_id')
                    ->relationship('karyawan', 'id')
                    ->required(),
                Forms\Components\TextInput::make('kuantitas')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('barangKeluar.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('karyawan.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kuantitas')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ManagePemakaianLapangans::route('/'),
        ];
    }
}
