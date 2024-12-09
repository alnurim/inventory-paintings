<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LokasiResource\Pages;
use App\Filament\Resources\LokasiResource\RelationManagers;
use App\Models\Lokasi;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LokasiResource extends Resource
{
    protected static ?string $model = Lokasi::class;
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $label = 'Lokasi';
    protected static ?string $navigationGroup = 'Zona dan Lokasi';
    protected static ?string $activeNavigationIcon = 'heroicon-s-map';
    protected static ?int $navigationSort = 11;
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() < 4 ? 'danger' : 'info';
    }
    protected static ?string $navigationBadgeTooltip = 'Total Lokasi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')
                    ->label('Nama Lokasi')
                    ->placeholder('Masukkan Nama lokasi')
                    ->minLength(3)
                    ->maxLength(45)
                    ->required(),

                Select::make('tipe_lokasi_id')
                    ->label('Area')
                    ->placeholder('Pilih Area')
                    ->relationship('tipeLokasi', 'nama')
                    ->native(false)
                    ->preload()
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Lokasi')
                    ->searchable(),

                TextColumn::make('tipeLokasi.nama')
                    ->label('Area')
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
            'index' => Pages\ManageLokasis::route('/'),
        ];
    }
}
