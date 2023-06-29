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
        $manipulation->load(['slots' => function ($builder) {
            $builder->where('start', '>=', Carbon::now())
                ->whereDoesntHave(('booking'));
        }]);
        return view('slots', [
            'manipulationId'           => $manipulation->id,
            'manipulationName'         => $manipulation->name,
            'manipulationRequirements' => $manipulation->requirements,
            'slots'                    => $manipulation->slots
                ->map(fn (Slot $s) => [
                    'id'    => $s->id,
                    'day'   => $s->start->format('Y-m-d'),
                    'start' => $s->start->format('H:i'),
                    'end'   => $s->end->format('H:i'),
                ])
                ->groupBy('day')
        ]);
    }
}
