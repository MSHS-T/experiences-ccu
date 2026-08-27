<?php

namespace App\Filament\Resources\PlateauResource\Pages;

use Filament\Actions\EditAction;
use App\Filament\Resources\PlateauResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPlateau extends ViewRecord
{
    protected static string $resource = PlateauResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
