<?php

namespace App\Livewire;

use App\Livewire\Traits\DisplaysSlots;
use App\Livewire\Traits\InteractsWithSlots;
use App\Models\Manipulation;
use App\Models\Slot;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;

class ManipulationCalendar extends Component implements HasForms, HasActions
{
    use DisplaysSlots;
    use InteractsWithSlots;

    public $colors = [];
    public Manipulation $manipulation;

    public function mount(int $manipulationId)
    {
        $this->manipulation = Manipulation::find($manipulationId);
    }

    public function fetchEvents(array $fetchInfo): array
    {
        $events = $this->manipulation
            ->slots()
            ->where('start', '>=', $fetchInfo['start'])
            ->where('end', '<=', $fetchInfo['end'])
            ->get()
            ->map(
                fn (Slot $slot) => [
                    'id'    => $slot->id,
                    'title' => $this->slotLabel($slot, showPlateau: false),
                    'start' => $slot->start,
                    'end'   => $slot->end,
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
        return view('livewire.manipulation-calendar');
    }
}
