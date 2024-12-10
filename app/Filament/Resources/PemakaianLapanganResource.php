<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PemakaianLapanganResource\Pages;
use App\Filament\Resources\PemakaianLapanganResource\RelationManagers;
use App\Models\PemakaianLapangan;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
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
                Select::make('barang_keluar_id')
                    ->label('Material')
                    ->relationship('barangKeluar', 'id')
                    ->required(),
                Select::make('karyawan_id')
                    ->relationship('karyawan', 'id')
                    ->required(),
                TextInput::make('kuantitas')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('barangKeluar.id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('karyawan.id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('kuantitas')
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
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
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
            'index' => Pages\ManagePemakaianLapangans::route('/'),
        ];
    }
}
