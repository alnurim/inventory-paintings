<?php

namespace App\Filament\Resources\TipeLokasiResource\Pages;

use App\Filament\Resources\TipeLokasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTipeLokasis extends ManageRecords
{
    protected static string $resource = TipeLokasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
