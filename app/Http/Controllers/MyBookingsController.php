<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingHistory;
use App\Utils\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MyBookingsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if (blank($subjectEmail = Session::get('subject_email', null))) {
            return redirect()->route('subject_login');
        }

        $subject = Subject::find($subjectEmail);

        return view('my-bookings', [
            'bookings' => $subject?->futureBookings() ?? [],
            'history'  => $subject?->history() ?? [],
        ]);
    }
}
