<?php

namespace App\Filament\Resources\ManipulationResource\Pages;

use App\Filament\Resources\ManipulationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewManipulation extends ViewRecord
{
    protected static string $resource = ManipulationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
