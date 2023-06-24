<?php

namespace App\Http\Controllers;

use App\Models\Manipulation;
use Illuminate\Http\Request;

class ManipulationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $manipulations = Manipulation::with(['slots', 'slots.booking'])
            ->visibleForParticipants()
            ->get();
        return view('manipulations', [
            'manipulations'     => $manipulations
        ]);
    }
}
