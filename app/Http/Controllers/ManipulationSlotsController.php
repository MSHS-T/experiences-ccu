<?php

namespace App\Http\Controllers;

use App\Models\Manipulation;
use App\Models\Slot;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ManipulationSlotsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Manipulation $manipulation)
    {
        $maxBookingPerSlot = $manipulation->max_booking_per_slot;
        $manipulation->load(['slots' => function ($builder) use ($maxBookingPerSlot) {
            $builder->where('start', '>=', Carbon::now())
                ->has('bookings', '<', $maxBookingPerSlot);
        }]);
        return view('slots', [
            'manipulationId'           => $manipulation->id,
            'manipulationName'         => $manipulation->name,
            'manipulationRequirements' => $manipulation->requirements,
            'slots'                    => $manipulation->slots
                ->map(fn(Slot $s) => [
                    'id'             => $s->id,
                    'day'            => $s->start->format('Y-m-d'),
                    'formatted_date' => $s->start->translatedFormat('l d F Y'),
                    'start'          => $s->start->format('H:i'),
                    'end'            => $s->end->format('H:i'),
                ])
                ->groupBy('day')
        ]);
    }
}
