<?php

namespace App\Filament\Resources\ManipulationResource\Pages;

use App\Filament\Resources\ManipulationResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class CreateManipulation extends CreateRecord
{
    protected static string $resource = ManipulationResource::class;

    protected function getHeaderWidgets(): array
    {
        if (Auth::user()->hasRole('administrator')) {
            return [];
        } else {
            return [
                ManipulationResource\Widgets\AttributionOverview::class,
            ];
        }
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (filled($data['requirements'])) {
            $data['requirements'] = collect($data['requirements'])
                ->map(fn ($r) => ['text' => $r])
                ->all();
        }

        return $data;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['requirements'] = collect($data['requirements'])
            ->map(fn ($r) => array_values(Arr::wrap($r)))
            ->flatten()
            ->unique()
            ->all();

        return $data;
    }
}
