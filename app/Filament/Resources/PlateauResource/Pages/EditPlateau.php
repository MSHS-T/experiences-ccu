<?php

namespace App\Filament\Resources\PlateauResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\PlateauResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlateau extends EditRecord
{
    protected static string $resource = PlateauResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
