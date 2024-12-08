<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Models\Role;
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
use Illuminate\Support\Facades\Auth;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $label = 'Level Pengguna';
    protected static ?string $navigationGroup = 'Kelola Pengguna';
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $activeNavigationIcon = 'heroicon-s-identification';
    protected static ?int $navigationSort = 19;
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() < 2 ? 'danger' : 'info';
    }
    protected static ?string $navigationBadgeTooltip = 'Total Level Pengguna';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Level')
                    ->placeholder('Masukkan Nama Level Pengguna')
                    ->minLength(3)
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->autofocus()
                    ->inlineLabel()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Level')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->authorize(function ($record) {
                            // Pastikan bahwa user tidak bisa menghapus role dirinya sendiri
                            return Auth::id() !== $record->id; // Jika ini untuk role, ganti dengan kondisi role
                        })
                        ->using(function ($record) {
                            // Pastikan role yang sedang dipilih bukan role pengguna yang sedang login
                            if (Auth::user()->hasRole($record->name)) {
                                session()->flash('error', 'You cannot delete your own role.');
                                return false; // Mencegah penghapusan role
                            }

                            $record->delete(); // Lanjutkan penghapusan role jika bukan role yang sedang login
                        })
                        ->requiresConfirmation(),
                ])->icon('heroicon-m-ellipsis-horizontal')
                    ->color('info')
                    ->tooltip('Aksi')
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->using(function ($records) {
                            // Filter keluar role yang digunakan oleh pengguna yang sedang login
                            $rolesToDelete = $records->reject(function ($record) {
                                return Auth::user()->hasRole($record->name); // Cek apakah role tersebut dimiliki oleh pengguna yang login
                            });

                            // Lakukan penghapusan role yang tidak terkait dengan pengguna yang sedang login
                            $rolesToDelete->each(function ($record) {
                                $record->delete();
                            });

                            // Pesan sukses
                            session()->flash('message', 'Selected roles were deleted, except for your own role.');
                        })
                        ->requiresConfirmation(),
                ])
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRoles::route('/'),
        ];
    }
}
