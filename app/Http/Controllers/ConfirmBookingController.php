<?php

namespace App\Http\Controllers;

use App\Actions\CancelBookingAction;
use App\Actions\ConfirmBookingAction;
use App\Mail\BookingConfirmation;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use MagicLink\MagicLink;

class ConfirmBookingController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Booking $booking, Request $request)
    {
        $confirmAction = new ConfirmBookingAction($booking);

        $confirmationUrl = MagicLink::create($confirmAction, (int) now()->diffInSeconds($booking->confirm_before, true), 1)->url;
        $cancellationUrl = MagicLink::create(new CancelBookingAction($booking), (int) now()->diffInSeconds($booking->slot->start, true), 1)->url;

        Mail::to($booking->email)->send(new BookingConfirmation($booking, $confirmationUrl, $cancellationUrl));

        return redirect()->route('my_bookings')->with('success', __('public.my_bookings.confirmation_email_sent'));
    }
}
