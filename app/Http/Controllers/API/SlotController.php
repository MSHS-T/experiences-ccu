<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Manipulation;
use App\Slot;
use App\User;
use Carbon\Carbon;

class SlotController extends Controller
{
    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwtauth');

        // TODO : add authorization gates (https://laravel.com/docs/6.x/authorization)
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Manipulation $manipulation)
    {
        return $manipulation->slots()->get();
    }

    /**
     * Generates slots for a manipulation
     *
     * @param  \App\Manipulation         $manipulation
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generate(Manipulation $manipulation)
    {
        // Generate all slots if manipulation doesn't have any
        if (!$manipulation->slots->isEmpty()) {
            return [];
        }
        $hours = $manipulation->available_hours;
        $duration = $manipulation->duration;

        $current_date = new Carbon($manipulation->start_date);
        $current_date->startOfDay();
        $slots = [];
        do {
            $day = $current_date->shortEnglishDayOfWeek;
            if ($hours[$day]['enabled'] === true) {
                foreach (['am', 'pm'] as $ampm) {
                    if ($hours[$day]['am'] === true) {
                        list($start_h, $start_m) = array_map('intval', explode(':', $hours[$day]['start_' . $ampm]));
                        list($end_h, $end_m) = array_map('intval', explode(':', $hours[$day]['end_' . $ampm]));
                        $current_date->setTime($start_h, $start_m, 0);
                        $limit = $current_date->copy()->setTime($end_h, $end_m, 0);
                        while ($current_date->diffInMinutes($limit) >= $duration) {
                            $next_date = $current_date->copy()->addMinutes($duration);
                            $slots[] = ['start' => $current_date, 'end' => $next_date];
                            $current_date = $next_date;
                        }
                    }
                }
            }
            $current_date->startOfDay()->addDay();
        } while (count($slots) < $manipulation->target_slots); // TODO : Use overbooking percentage from settings

        $manipulation->slots()->createMany($slots);

        return $manipulation->slots()->get();
    }

    /**
     * Store a single new slot in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Manipulation $manipulation, Request $request)
    {
        // Validate data
        $data = $request->validate([
            'start' => 'required|date_format:Y-m-d H:i:s',
            'end'   => 'required|date_format:Y-m-d H:i:s'
        ]);
        // Check for overlap with existing slots
        $noOverlap = $manipulation->slots->every(function ($slot) use ($data) {
            return !Carbon::create($data['start'])->betweenExcluded($slot->start, $slot->end)
                && !Carbon::create($data['end'])->betweenExcluded($slot->start, $slot->end);
        });
        if ($noOverlap) {
            $manipulation->slots()->create($data);
            return 201;
        }
        return response(null, 400)->json(['message' => 'Impossible de chevaucher plusieurs créneaux']);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Slot                 $slot
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Slot $slot, Request $request)
    {
        // Validate data
        $data = $request->validate([
            'subject_first_name' => 'required|string',
            'subject_last_name'  => 'required|string',
            'subject_email'      => 'required|email'
        ]);
        $slot->fill($data);
        return $slot;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Slot::findOrFail($id)->delete();
        return 204;
    }
}
