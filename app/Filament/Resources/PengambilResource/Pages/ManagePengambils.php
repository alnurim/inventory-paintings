<?php

namespace App\Filament\Resources\PengambilResource\Pages;

use App\Filament\Resources\PengambilResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePengambils extends ManageRecords
{
    protected static string $resource = PengambilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
