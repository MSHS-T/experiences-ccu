<?php

namespace App\Livewire;

use App\Livewire\Traits\DisplaysSlots;
use App\Livewire\Traits\InteractsWithSlots;
use App\Models\Manipulation;
use App\Models\Plateau;
use App\Models\Slot;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;

class SlotCalendar extends Component implements HasForms, HasActions
{
    use DisplaysSlots;
    use InteractsWithSlots;

    public ?Plateau $plateau;
    public ?Manipulation $manipulation;


    public $colors = [];
    public $plateaux = [];
    public $checkedPlateaux;

    public function mount(int $plateauId = null, int $manipulationId = null)
    {
        if (filled($plateauId)) {
            $this->plateau = Plateau::find($plateauId);
            ray('test');
        } else if (filled($manipulationId)) {
            $this->manipulation = Manipulation::find($manipulationId);
        } else {
            $this->plateaux = Plateau::all()
                ->map(fn (Plateau $plateau) => [
                    'id'    => $plateau->id,
                    'name'  => $plateau->name,
                    'color' => $plateau->color,
                ])
                ->toArray();
            $this->checkedPlateaux = array_fill_keys(array_column($this->plateaux, 'id'), true);
        }
    }

    public function fetchEvents(array $fetchInfo): array
    {
        $events = Slot::query()
            ->where('start', '>=', $fetchInfo['start'])
            ->where('end', '<=', $fetchInfo['end'])
            ->get()
            ->filter(fn (Slot $slot) => $this->shouldDisplaySlot($slot))
            ->map(
                fn (Slot $slot) => [
                    'id'         => $slot->id,
                    'title'      => $this->slotLabel($slot, showPlateau: !isset($this->plateau)),
                    'start'      => $slot->start,
                    'end'        => $slot->end,
                    // 'resourceId' => $slot->manipulation->plateau->id,
                    ...$this->slotColor($slot),
                ]
            );
        ray($events->values()->all());
        return $events->values()->all();
    }

    public function shouldDisplaySlot(Slot $slot): bool
    {
        if (isset($this->plateau) && filled($this->plateau)) {
            return $slot->manipulation->plateau->id === $this->plateau->id;
        }
        if (isset($this->manipulation) && filled($this->manipulation)) {
            return $slot->manipulation->id === $this->manipulation->id;
        }
        return $this->checkedPlateaux[$slot->manipulation->plateau->id] ?? false;
    }

    public function togglePlateau($plateau)
    {
        $this->refreshRecords();
    }

    public function render()
    {
        return view('livewire.slot-calendar', [
            'showPlateaux' => !isset($this->plateau) && !isset($this->manipulation),
        ]);
    }
}
