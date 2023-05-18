<?php

namespace App\Filament\Resources\ManipulationResource\Pages;

use App\Filament\Resources\ManipulationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;

class EditManipulation extends EditRecord
{
    protected static string $resource = ManipulationResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (filled($data['requirements'])) {
            $data['requirements'] = collect($data['requirements'])
                ->map(fn ($r) => ['text' => $r])
                ->all();
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['requirements'] = collect($data['requirements'])
            ->map(fn ($r) => array_values(Arr::wrap($r)))
            ->flatten()
            ->unique()
            ->all();

        return $data;
    }

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
