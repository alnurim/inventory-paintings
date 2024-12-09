<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KaryawanResource\Pages;
use App\Filament\Resources\KaryawanResource\RelationManagers;
use App\Models\Karyawan;
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

class KaryawanResource extends Resource
{
    protected static ?string $model = Karyawan::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $label = 'Karyawan';
    protected static ?string $navigationGroup = 'Aktivitas dan Penggunaan';
    protected static ?string $activeNavigationIcon = 'heroicon-s-user-group';
    protected static ?int $navigationSort = 10;
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() < 4 ? 'danger' : 'info';
    }
    protected static ?string $navigationBadgeTooltip = 'Total Karyawan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('npk')
                    ->label('Nomor Pekerja Kontraktor (NPK)')
                    ->placeholder('Masukkan Nomor Pekerja')
                    ->minLength(3)
                    ->maxLength(20)
                    ->columnSpanFull()
                    ->required(),

                TextInput::make('nama')
                    ->label('Nama Karyawan')
                    ->placeholder('Masukkan Nama Karyawan')
                    ->minLength(3)
                    ->maxLength(45)
                    ->required(),

                TextInput::make('jabatan')
                    ->label('Jabatan')
                    ->placeholder('Masukkan Jabatan Karyawan')
                    ->maxLength(20)
                    ->default('Kontraktor')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('npk')
                    ->label('Nomor Pekerja Kontraktor (NPK)')
                    ->searchable(),

                TextColumn::make('nama')
                    ->label('Nama Karyawan')
                    ->searchable(),

                TextColumn::make('jabatan')
                    ->label('Jabatan')
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
            'index' => Pages\ManageKaryawans::route('/'),
        ];
    }
}
