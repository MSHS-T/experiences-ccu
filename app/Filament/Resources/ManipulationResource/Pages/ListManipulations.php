<?php

namespace App\Filament\Resources\ManipulationResource\Pages;

use App\Filament\Resources\ManipulationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListManipulations extends ListRecords
{
    protected static string $resource = ManipulationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
