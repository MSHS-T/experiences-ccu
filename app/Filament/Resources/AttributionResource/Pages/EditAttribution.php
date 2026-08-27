<?php

namespace App\Filament\Resources\AttributionResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\AttributionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttribution extends EditRecord
{
    protected static string $resource = AttributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
