<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TipeLokasiResource\Pages;
use App\Filament\Resources\TipeLokasiResource\RelationManagers;
use App\Models\TipeLokasi;
use Filament\Forms;
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

class TipeLokasiResource extends Resource
{
    protected static ?string $model = TipeLokasi::class;
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $label = 'Data Area';
    protected static ?string $navigationGroup = 'Zona dan Lokasi';
    protected static ?string $activeNavigationIcon = 'heroicon-s-map-pin';
    protected static ?int $navigationSort = 12;
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() < 4 ? 'danger' : 'info';
    }
    protected static ?string $navigationBadgeTooltip = 'Total Area';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')
                    ->label('Nama Area')
                    ->placeholder('Masukkan Nama Area')
                    ->minLength(3)
                    ->maxLength(45)
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Area')
                    ->searchable(),
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
            'index' => Pages\ManageTipeLokasis::route('/'),
        ];
    }
}
