<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Manipulation;
use App\Slot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

use PDF;

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
     * Export a listing of the resource for the given date.
     *
     * @return \Illuminate\Http\Response
     */
    public function export(Manipulation $manipulation, Request $request)
    {
        // Validate data
        $data = $request->validate([
            'date' => 'required|date_format:Y-m-d|after_or_equal:today'
        ]);
        $date = $data['date'];
        $slots = $manipulation->slots->filter(function ($slot) use ($date) {
            return $slot->start->format('Y-m-d') === $date;
        })->sortBy('start')->values();
        if ($slots->isEmpty()) {
            return response('Aucun créneau trouvé', 404);
        }

        $title = implode(' - ', [
            config('app.name'),
            $manipulation->name,
            "Appel " . $date
        ]);

        $pdf = PDF::loadView('export.call_sheet', compact('date', 'manipulation', 'slots'));
        return $pdf->download($title . '.pdf');
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
