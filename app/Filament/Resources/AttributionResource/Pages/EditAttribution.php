<?php

namespace App\Filament\Resources\AttributionResource\Pages;

use App\Filament\Resources\AttributionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttribution extends EditRecord
{
    protected static string $resource = AttributionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
