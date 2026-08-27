<?php

namespace App\Filament\Resources\ManipulationResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use App\Filament\Resources\ManipulationResource;
use App\Models\Manipulation;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListManipulations extends ListRecords
{
    protected static string $resource = ManipulationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $query = Manipulation::query()
            ->when(
                Auth::user()->hasRole('manipulation_manager'),
                fn(Builder $query) => $query->whereHas('users', fn(Builder $query) => $query->where('id', Auth::id()))
            );

        return [
            'all' => Tab::make('Toutes')
                ->badge($query->count()),
            'active' => Tab::make('Actives')
                ->modifyQueryUsing(fn(Builder $query) => $query->active())
                ->badge($query->active()->count()),
            'inactive' => Tab::make('Archivées')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('archived', true))
                ->badge($query->where('archived', true)->count()),
        ];
    }
}
