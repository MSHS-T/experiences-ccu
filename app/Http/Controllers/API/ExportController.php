<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Manipulation;
use PDF;

class ExportController extends Controller
{
    /**
     * Export a listing of the resource for the given date.
     *
     * @return \Illuminate\Http\Response
     */
    public function callSheet(Manipulation $manipulation, Request $request)
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
}
