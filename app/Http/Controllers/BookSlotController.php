<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookSlotRequest;
use App\Models\Slot;
use Illuminate\Http\Request;

class BookSlotController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Slot $slot, BookSlotRequest $request)
    {
        //
    }
}
