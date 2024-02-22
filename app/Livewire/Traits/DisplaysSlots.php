<?php

namespace App\Livewire\Traits;

use App\Models\Plateau;
use App\Models\Slot;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Auth;
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
                // Booking confirmed : plateau color (100% opacity)
                return [
                    'backgroundColor' => $baseColor,
                    'borderColor'     => $baseColor
                ];
            } else {
                // Booking unconfirmed : striped plateau color (50% opacity) with 100% opacity
                return [
                    'background'  => "linear-gradient(135deg, {$baseColor} 12.50%, #808080 12.50%, #808080 25%, {$baseColor} 25%, {$baseColor} 37.50%, #808080 37.50%, #808080 50%, {$baseColor} 50%, {$baseColor} 62.50%, #808080 62.50%, #808080 75%, {$baseColor} 75%, {$baseColor} 87.50%, #808080 87.50%, #808080 100%)",
                    'borderColor' => $baseColor
                ];
            }
        }

        // Slot not booked (default) : gray
        return [
            'backgroundColor' => '#808080',
            'borderColor' => $baseColor,
        ];
    }

    protected function slotLabel(Slot $slot, bool $showPlateau = false): string
    {
        $prefix = ((!$slot->booking || $slot->booking->confirmed) ? '' : '[?] ');
        $suffix = $showPlateau ? (' (' . $slot->manipulation->plateau->name . ')') : '';
        switch (Auth::user()->roles->first()->name) {
            case 'administrator':
            case 'plateau_manager':
                return $prefix
                    . $slot->manipulation->users->first()->name
                    . $suffix;
            case 'manipulation_manager':
                return $prefix
                    . $slot->manipulation->name
                    . $suffix;
        }
        return '';
    }
}
