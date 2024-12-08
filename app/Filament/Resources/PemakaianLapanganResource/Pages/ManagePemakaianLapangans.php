<?php

namespace App\Filament\Resources\PemakaianLapanganResource\Pages;

use App\Filament\Resources\PemakaianLapanganResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePemakaianLapangans extends ManageRecords
{
    protected static string $resource = PemakaianLapanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
