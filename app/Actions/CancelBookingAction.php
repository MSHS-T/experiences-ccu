<?php

namespace App\Actions;

use App\Models\Booking;
use Illuminate\Support\Facades\Session;
use MagicLink\Actions\ActionAbstract;

class CancelBookingAction extends ActionAbstract
{
    public function __construct(public Booking $booking)
    {
    }

    public function run()
    {
        // Do something
        $this->booking->delete();

        $targetRoute = Session::has('subject_email')
            ? 'my_bookings'
            : 'subject_login';

        return response()->redirectToRoute($targetRoute)
            ->with('success', 'Votre inscription a bien été annulée.');
    }
}
