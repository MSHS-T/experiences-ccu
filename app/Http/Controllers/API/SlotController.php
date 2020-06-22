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

        $manipulation->generateSlots($fromDate, $toDate);

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
