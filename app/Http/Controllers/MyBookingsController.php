<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingHistory;
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

        $allBookings = Booking::with(['slot', 'slot.manipulation'])
            ->where('email', $subjectEmail)
            ->get();

        // List future bookings
        $futureBookings = $allBookings->filter(fn (Booking $booking) => $booking->slot->start > now());

        // Get booking history
        $bookingHistory = BookingHistory::where('hashed_email', md5($subjectEmail))->first();

        $history = [
            'made'                => $bookingHistory ? $bookingHistory->booking_made : 0,
            'confirmed'           => $bookingHistory ? $bookingHistory->booking_confirmed : 0,
            'confirmed_honored'   => $bookingHistory ? $bookingHistory->booking_confirmed_honored : 0,
            'unconfirmed_honored' => $bookingHistory ? $bookingHistory->booking_unconfirmed_honored : 0,
            'blocked'             => $bookingHistory ? $bookingHistory->blocked : false,
        ];

        // List past bookings (not archived yet) and add them to booking history
        $allBookings->filter(fn (Booking $booking) => $booking->slot->start > now())
            ->each(function (Booking $booking) use (&$history) {
                $history['made']                += 1;
                $history['confirmed']           += $booking->confirmed ? 1 : 0;
                $history['confirmed_honored']   += ($booking->confirmed && $booking->honored) ? 1 : 0;
                $history['unconfirmed_honored'] += (!$booking->confirmed && $booking->honored) ? 1 : 0;
            });

        return view('my-bookings', [
            'bookings' => $futureBookings,
            'history'  => $history,
        ]);
    }
}
