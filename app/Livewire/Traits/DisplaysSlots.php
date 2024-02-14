<?php

namespace App\Livewire\Traits;

use App\Models\Plateau;
use App\Models\Slot;
use Filament\Support\Colors\Color;
use Spatie\Color\Rgb;

trait DisplaysSlots
{
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

    protected function slotColor(Slot $slot): array
    {
        $baseColor = $this->plateauColor($slot->manipulation->plateau);
        if (filled($slot->booking)) {
            if ($slot->booking->confirmed) {
                // Booking confirmed : full plateau color (50% opacity)
                return [
                    'backgroundColor' => $baseColor . '66',
                    'borderColor'     => $baseColor
                ];
            } else {
                // Booking unconfirmed : striped plateau color (50% opacity) with gray
                return [
                    'background'  => "linear-gradient(135deg, {$baseColor}66 25%, #808080 25%, #808080 50%, {$baseColor}66 50%, {$baseColor}66 75%, #808080 75%, #808080 100%)",
                    'borderColor' => $baseColor
                ];
            }
        }

        // Slot not booked (default) : gray (50% opacity) background
        return [
            'backgroundColor' => '#80808066',
            'borderColor' => $baseColor,
        ];
    }

    protected function slotLabel(Slot $slot, bool $showPlateau = false): string
    {
        $suffix = $showPlateau ? (' (' . $slot->manipulation->plateau->name . ')') : '';
        if (filled($slot->booking)) {
            return ($slot->booking->confirmed ? '' : '[?] ')
                . $slot->booking->name
                . $suffix;
        }

        // Slot not booked (default) : slot ID + plateau (if requested)
        return '#' . $slot->id . $suffix;
    }
}
