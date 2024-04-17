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

        Session::remove('subject_email');

        return response()->redirectToRoute('subject_login')
            ->with('success', 'Votre inscription a bien été annulée.');
    }
}
