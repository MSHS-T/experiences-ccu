<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Manipulation;
use App\Slot;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class BookingController extends Controller
{
    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwtauth');

        // TODO : add authorization gates (https://laravel.com/docs/6.x/authorization)
    }

    /**
     * Update multiple bookings
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Validate data
        $data = $request->validate([
            'attendance'           => 'required|array|min:1',
            'attendance.*.slot'    => 'required|int|exists:slots,id',
            'attendance.*.honored' => 'required|boolean'
        ]);
        foreach ($data['attendance'] as $attendance) {
            $slot = Slot::findOrFail($attendance['slot']);
            $slot->booking->honored = $attendance['honored'];
            $slot->push();
        }
        return response('', 204);
    }
}
