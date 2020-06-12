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
use Illuminate\Support\Facades\Response;

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
        return $manipulation->slots()->orderBy('start')->get();
    }

    /**
     * Generates slots for a manipulation
     *
     * @param  \App\Manipulation         $manipulation
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generate(Manipulation $manipulation, Request $request)
    {
        // If given a 'date' parameter, we generate slots to fill the available hours of that day
        $fromDate = $request->input('date', false);
        // If a date was given we will only fill that day
        $toDate = $fromDate;

        // Existing slots
        $existingSlots = $manipulation->slots()->orderBy('start')->get();

        // If no date was given, we generate until quota is met
        if ($fromDate === false) {
            // If no slots exist we start at the manipulation start date
            if ($manipulation->slots->isEmpty()) {
                $fromDate = $manipulation->start_date;
            }
            // If slots already exist, we start the day after the last slot
            else {
                $fromDate = $existingSlots->last()->start->addDay();
            }
        }

        // Generate all slots if manipulation doesn't have any
        // TODO : If manipulation has slots, we generate after the last one until quota is met
        $hours = $manipulation->available_hours;
        $duration = $manipulation->duration;

        $current_date = new Carbon($fromDate);
        $current_date->startOfDay();
        $slots = [];
        do {
            $day = $current_date->shortEnglishDayOfWeek;
            if ($hours[$day]['enabled'] === true) {
                foreach (['am', 'pm'] as $ampm) {
                    if ($hours[$day][$ampm] === true) {
                        list($start_h, $start_m) = array_map('intval', explode(':', $hours[$day]['start_' . $ampm]));
                        list($end_h, $end_m) = array_map('intval', explode(':', $hours[$day]['end_' . $ampm]));
                        $current_date->setTime($start_h, $start_m, 0);
                        $limit = $current_date->copy()->setTime($end_h, $end_m, 0);
                        while ($current_date->diffInMinutes($limit) >= $duration) {
                            $next_date = $current_date->copy()->addMinutes($duration);
                            $slots[] = ['start' => $current_date, 'end' => $next_date];
                            $current_date = $next_date->copy();
                        }
                    }
                }
            }
            $current_date->startOfDay()->addDay();
            if ($toDate !== false && $current_date->greaterThan(Carbon::create($toDate)->endOfDay())) {
                break;
            }
        } while ((count($slots) + count($existingSlots)) < $manipulation->target_slots); // TODO : Use overbooking percentage from settings

        // Filter slots against existing ones (if any) to avoid duplicates
        $slots = array_filter($slots, function ($s) use ($existingSlots) {
            return $existingSlots->isEmpty() || $existingSlots->every(function ($slot) use ($s) {
                return $s['start']->greaterThanOrEqualTo($slot->end)
                    || $s['end']->lessThanOrEqualTo($slot->start);
            });
        });

        $manipulation->slots()->createMany($slots);

        return $manipulation->slots()->orderBy('start')->get();
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
            'end'   => 'required|date_format:Y-m-d H:i:s|after_or_equal:start'
        ]);
        // Check for overlap with existing slots
        $noOverlap = $manipulation->slots->every(function ($slot) use ($data) {
            return Carbon::create($data['start'])->greaterThanOrEqualTo($slot->end)
                || Carbon::create($data['end'])->lessThanOrEqualTo($slot->start);
        });
        if ($noOverlap) {
            $manipulation->slots()->create($data);
            return 201;
        }
        return Response::json(['message' => 'Impossible de chevaucher plusieurs créneaux'], 400);
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
        $slot->save();
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
        // TODO : If slot is booked, send notification to subject before deleting
        return 204;
    }
}
