<?php

namespace App\Http\Controllers;

use App\Models\Manipulation;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(GeneralSettings $settings)
    {
        $manipulations = Manipulation::with(['slots', 'slots.booking'])
            ->visibleForParticipants()
            ->limit(6)
            ->get();
        return view('home', [
            'manipulations'        => $manipulations,
            'selectedManipulation' => $manipulations->first()?->id,
            'presentation_text'    => $settings->presentation_text,
            'access_instructions'  => $settings->access_instructions,
        ]);
    }
}
