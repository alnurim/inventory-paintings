<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengambilResource\Pages;
use App\Filament\Resources\PengambilResource\RelationManagers;
use App\Models\Pengambil;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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
                Forms\Components\Select::make('karyawan_id')
                    ->relationship('karyawan', 'id')
                    ->required(),
                Forms\Components\Select::make('peminjaman_id')
                    ->relationship('peminjaman', 'id')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('karyawan.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('peminjaman.id')
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
            'index' => Pages\ManagePengambils::route('/'),
        ];
    }
}
