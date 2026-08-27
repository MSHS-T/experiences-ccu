<?php

namespace App\Http\Controllers;

use App\Actions\CancelBookingAction;
use App\Actions\ConfirmBookingAction;
use App\Http\Requests\BookSlotRequest;
use App\Mail\BookingConfirmation;
use App\Models\Slot;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use MagicLink\MagicLink;

class BookSlotController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Slot $slot, BookSlotRequest $request, GeneralSettings $settings)
    {
        $confirmationCode = md5(time() . $request->first_name . $request->last_name . $request->email);
        $confirmBefore = $slot->start->subHours($settings->booking_confirmation_delay);

        $booking = $slot->bookings()->create([
            'first_name'        => $request->first_name,
            'last_name'         => $request->last_name,
            'email'             => $request->email,
            'confirmed'         => false,
            'confirmation_code' => $confirmationCode,
            'confirm_before'    => $confirmBefore,
            'honored'           => null
        ]);

        $confirmAction = new ConfirmBookingAction($booking);

        if (App::isLocal()) {
            // return $confirmAction->run();
        }

        $confirmationUrl = MagicLink::create($confirmAction, (int) now()->diffInSeconds($confirmBefore, true), 1)->url;
        $cancellationUrl = MagicLink::create(new CancelBookingAction($booking), (int) now()->diffInSeconds($slot->start, true), 1)->url;

        Mail::to($request->email)->send(new BookingConfirmation($booking, $confirmationUrl, $cancellationUrl));

        return redirect()->route('subject_login')->with('success', __('public.messages.booking_created'));
    }
}
