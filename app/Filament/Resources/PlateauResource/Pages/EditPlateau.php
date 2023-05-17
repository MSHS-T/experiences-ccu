<?php

namespace App\Filament\Resources\PlateauResource\Pages;

use App\Filament\Resources\PlateauResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlateau extends EditRecord
{
    protected static string $resource = PlateauResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
