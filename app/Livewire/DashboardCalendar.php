<?php

namespace App\Livewire;

use App\Livewire\Traits\DisplaysSlots;
use App\Livewire\Traits\InteractsWithSlots;
use App\Models\Plateau;
use App\Models\Slot;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;

class DashboardCalendar extends Component implements HasForms, HasActions
{
    use DisplaysSlots;
    use InteractsWithSlots;

    public $colors = [];
    public $plateaux;
    public $checkedPlateaux;

    public function mount()
    {
        $this->plateaux = Plateau::all()
            ->map(fn (Plateau $plateau) => [
                'id'    => $plateau->id,
                'name'  => $plateau->name,
                'color' => $plateau->color,
            ])
            ->toArray();
        $this->checkedPlateaux = array_fill_keys(array_column($this->plateaux, 'id'), true);
    }

    public function fetchEvents(array $fetchInfo): array
    {
        $events = Slot::query()
            ->where('start', '>=', $fetchInfo['start'])
            ->where('end', '<=', $fetchInfo['end'])
            ->get()
            ->filter(fn (Slot $slot) => $this->checkedPlateaux[$slot->manipulation->plateau->id] ?? false)
            ->map(
                fn (Slot $slot) => [
                    'id'         => $slot->id,
                    'title'      => $this->slotLabel($slot, showPlateau: true),
                    'start'      => $slot->start,
                    'end'        => $slot->end,
                    'resourceId' => $slot->manipulation->plateau->id,
                    ...$this->slotColor($slot),
                ]
            );

        return $events->all();
    }

    public function togglePlateau($plateau)
    {
        $this->refreshRecords();
    }

    public function render()
    {
        return view('livewire.dashboard-calendar');
    }
}
