<?php

namespace App\Http\Controllers;

use App\Actions\CancelBookingAction;
use App\Mail\BookingCancellation;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use MagicLink\MagicLink;

class CancelBookingController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Booking $booking, Request $request)
    {
        $cancelAction = new CancelBookingAction($booking);
        $cancellationUrl = MagicLink::create($cancelAction, now()->diffInSeconds($booking->slot->start, true), 1)->url;

        Mail::to($booking->email)->send(new BookingCancellation($booking, $cancellationUrl));

        return redirect()->route('my_bookings')->with('success', __('public.my_bookings.cancellation_email_sent'));
    }
}
