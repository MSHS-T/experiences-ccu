<?php

namespace App\Filament\Resources\ManipulationResource\Pages;

use App\Filament\Resources\ManipulationResource;
use App\Models\Manipulation;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class EditManipulation extends EditRecord
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['requirements'] = collect($data['requirements'])
            ->map(fn ($r) => array_values(Arr::wrap($r)))
            ->flatten()
            ->unique()
            ->all();

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->hidden(fn (Manipulation $record) => !Auth::user()->can('manipulation.delete'))
                ->disabled(fn (Manipulation $record) => !Auth::user()->can('manipulation.delete') || $record->published),
        ];
    }
}
