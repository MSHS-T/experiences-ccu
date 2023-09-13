<?php

namespace App\Filament\Resources\PlateauResource\Pages;

use App\Filament\Resources\PlateauResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlateaux extends ListRecords
{
    protected static string $resource = PlateauResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
