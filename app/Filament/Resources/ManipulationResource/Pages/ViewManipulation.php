<?php

namespace App\Filament\Resources\ManipulationResource\Pages;

use App\Filament\Resources\ManipulationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewManipulation extends ViewRecord
{
    protected static string $resource = ManipulationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
