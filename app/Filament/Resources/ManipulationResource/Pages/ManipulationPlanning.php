<?php

namespace App\Filament\Resources\ManipulationResource\Pages;

use App\Filament\Resources\ManipulationResource;
use App\Models\Manipulation;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class ManipulationPlanning extends Page
{
    protected static string $resource = ManipulationResource::class;

    protected static string $view = 'filament.resources.manipulation-resource.pages.manipulation-planning';

    public Manipulation $manipulation;

    public function mount(Manipulation $record): void
    {
        $this->authorizeAccess();

        // $this->loadDefaultActiveTab();
        $this->manipulation = $record;
    }

    protected function authorizeAccess(): void
    {
        static::authorizeResourceAccess();
    }

    public function getTitle(): string | Htmlable
    {
        return __('admin.manipulation_planning', ['name' => $this->manipulation->name]);
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
