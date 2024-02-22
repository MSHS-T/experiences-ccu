<?php

namespace App\Filament\Resources\PlateauResource\Pages;

use App\Filament\Resources\PlateauResource;
use App\Models\Plateau;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class PlateauPlanning extends Page
{
    protected static string $resource = PlateauResource::class;

    protected static string $view = 'filament.resources.plateau-resource.pages.plateau-planning';

    public Plateau $plateau;

    public function mount(Plateau $record): void
    {
        $this->authorizeAccess();

        // $this->loadDefaultActiveTab();
        $this->plateau = $record;
    }

    protected function authorizeAccess(): void
    {
        static::authorizeResourceAccess();
    }

    public function getTitle(): string | Htmlable
    {
        return __('admin.plateau_planning', ['name' => $this->plateau->name]);
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();

        $breadcrumb = $this->getBreadcrumb();

        // ray(request()->rou('record'));

        return [
            $resource::getUrl() => $resource::getBreadcrumb(),
            ...(filled($breadcrumb) ? [$breadcrumb] : []),
        ];
    }
}
