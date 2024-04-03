<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MyBookingsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if (blank(Session::get('subject_email', null))) {
            return redirect()->route('subject_login');
        }

        return view('my-bookings', [
            'bookings' => []
        ]);
    }
}
