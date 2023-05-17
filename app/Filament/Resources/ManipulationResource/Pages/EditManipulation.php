<?php

namespace App\Filament\Resources\ManipulationResource\Pages;

use App\Filament\Resources\ManipulationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditManipulation extends EditRecord
{
    protected static string $resource = ManipulationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
