<?php

namespace App\Livewire;

use App\Models\Plateau;
use App\Models\Slot;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Saade\FilamentFullCalendar\Widgets\Concerns\InteractsWithEvents;
use Spatie\Color\Rgb;

class DashboardCalendar extends Component
{
    use InteractsWithEvents;
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
                    // TODO : title depending on booking
                    'title'      => $slot->id . ' (' . $slot->manipulation->plateau->name . ')',
                    'start'      => $slot->start,
                    'end'        => $slot->end,
                    // TODO : manage state of slot (booked+confirmed = plateau color, booked+not confirmed = striped plateau color with gray, not booked = only border in plateau color)
                    'color'      => $this->plateauColor($slot->manipulation->plateau) . '66',
                    'resourceId' => $slot->manipulation->plateau->id,
                ]
            );

        ray($events);

        return $events->all();
    }

    public function togglePlateau($plateau)
    {
        $this->refreshRecords();
    }

    protected function plateauColor(Plateau $plateau): string
    {
        if (filled($plateau->color)) {
            return $plateau->color;
        }
        if (blank($this->colors)) {
            $colors = array_values(Color::all());
            shuffle($colors);
            $this->colors = $colors;
        }

        $color = $this->colors[$plateau->id % count($this->colors)];
        return strval(Rgb::fromString('rgb(' . $color[800] . ')')->toHex());
    }

    public function render()
    {
        return view('livewire.dashboard-calendar');
    }
}
