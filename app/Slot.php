<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Slot extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'start',
        'end'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'start',
        'end'
    ];

    /**
     * The relationships that should always be included
     */
    protected $with = ['booking'];

    public function manipulation()
    {
        return $this->belongsTo('App\Manipulation', 'manipulation_id');
    }

    public function booking()
    {
        return $this->hasOne('App\Booking');
    }

    public function book(string $email, string $firstName, string $lastName)
    {
        $this->booking()->create([
            'first_name'        => $firstName,
            'last_name'         => $lastName,
            'email'             => $email,
            'confirmed'         => false,
            'confirmation_code' => Str::uuid(),
            'confirm_before'    => Carbon::now()->addHours(Setting::get('booking_confirmation_delay', 24)),
        ]);

        // TODO : send email to ask for confirmation

    }

    public function clearBooking(bool $sendNotification = true)
    {
        // Keep backup data if we need to send a notification
        $data = $this->attributesToArray();
        // Clear data
        $this->booking->delete();

        if ($sendNotification) {
            // TODO : send email notification
        }
    }
}
