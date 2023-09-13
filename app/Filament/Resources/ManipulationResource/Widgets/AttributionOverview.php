<?php

namespace App\Filament\Resources\ManipulationResource\Widgets;

use App\Models\Attribution;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class AttributionOverview extends Widget
{
    protected static string $view = 'filament.resources.manipulation-resource.widgets.attribution-overview';
    protected int | string | array $columnSpan = 'full';


    public array $attributionList;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        // Get the list of attributions for this user and all plateaux
        $this->attributionList = Attribution::notFinished()
            ->where('manipulation_manager_id', Auth::id())
            ->orderBy('start_date', 'asc')
            ->get()
            ->map(fn (Attribution $attribution) => [
                'start_date'       => $attribution->start_date->format('d/m/Y'),
                'end_date'         => $attribution->end_date->format('d/m/Y'),
                'plateau'          => $attribution->plateau->name,
                'allowed_halfdays' => $attribution->getSimplifiedAllowedHalfdaysDisplay()
            ])
            ->groupBy('plateau')
            ->toArray();
    }
}
