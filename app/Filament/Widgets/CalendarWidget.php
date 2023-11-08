<?php

namespace App\Filament\Widgets;

use App\Models\Slot;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Colors\Color;
use Filament\Widgets\Widget;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Spatie\Color\Rgb;

class CalendarWidget extends FullCalendarWidget
{
    protected int | string | array $columnSpan = 'full';

    public $colors = [];

    public function fetchEvents(array $fetchInfo): array
    {
        $events = Slot::query()
            ->where('start', '>=', $fetchInfo['start'])
            ->where('end', '<=', $fetchInfo['end'])
            ->get()
            ->map(
                fn (Slot $slot) => [
                    'title' => $slot->id . ' (' . $slot->manipulation->plateau->name . ')',
                    'start' => $slot->start,
                    'end' => $slot->end,
                    'color' => $this->eventColor($slot),
                    'resourceId' => $slot->manipulation->plateau->id
                ]
            );

        ray($events);

        // $plugin = \Saade\FilamentFullcalendar\FilamentFullCalendarPlugin::get();
        // $plugin->config(array_merge($plugin->getConfig(), [
        //     'resources' => $events->pluck('resourceName', 'resourceId')->map(fn ($title, $id) => compact('id', 'title'))->values()->all(),
        // ]));

        return $events->all();
    }

    protected function eventColor(Slot $slot): string
    {
        if (blank($this->colors)) {
            $colors = array_values(Color::all());
            shuffle($colors);
            $this->colors = $colors;
        }

        $color = $this->colors[$slot->manipulation->plateau->id % count($this->colors)];
        $shade = filled($slot->booking) ? 800 : 400;

        return strval(Rgb::fromString('rgb(' . $color[$shade] . ')')->toHex());
    }
}
