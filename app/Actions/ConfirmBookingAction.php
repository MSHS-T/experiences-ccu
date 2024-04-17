<?php

namespace App\Actions;

use App\Models\Booking;
use Illuminate\Support\Facades\Session;
use MagicLink\Actions\ActionAbstract;

class ConfirmBookingAction extends ActionAbstract
{
    public function __construct(public Booking $booking)
    {
    }

    public function run()
    {
        // Do something
        $this->booking->confirmed = true;
        $this->booking->save();

        Session::put('subject_email', $this->booking->email);

        return response()->redirectToRoute('my_bookings')
            ->with('success', 'Votre inscription a bien été annulée.');
    }
}
