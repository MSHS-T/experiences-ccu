<?php

namespace App\Filament\Resources\ManipulationResource\Pages;

use App\Filament\Resources\ManipulationResource;
use App\Models\Manipulation;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListManipulations extends ListRecords
{
    protected static string $resource = ManipulationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Toutes')
                ->badge(Manipulation::query()->count()),
            'active' => Tab::make('Actives')
                ->modifyQueryUsing(fn (Builder $query) => $query->active())
                ->badge(Manipulation::query()->active()->count()),
            'inactive' => Tab::make('Archivées')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('archived', true))
                ->badge(Manipulation::query()->where('archived', true)->count()),
        ];
    }
}
