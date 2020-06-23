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
        'end',
        'subject_first_name',
        'subject_last_name',
        'subject_email',
        'subject_confirmed',
        'subject_confirmation_code',
        'subject_confirm_before'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'start',
        'end',
        'subject_confirm_before'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */

    public function manipulation()
    {
        return $this->belongsTo('App\Manipulation', 'manipulation_id');
    }

    public function book(string $email, string $firstName, string $lastName)
    {
        // Fill data
        $this->subject_email = $email;
        $this->subject_first_name = $firstName;
        $this->subject_last_name = $lastName;
        // Add confirmation data
        $this->subject_confirmed = false;
        $this->subject_confirmation_code = Str::uuid();
        $this->subject_confirm_before = Carbon::now()->addHours(Setting::get('booking_confirmation_delay', 24));

        $this->save();

        // TODO : send email to ask for confirmation

    }

    public function clearBooking(bool $sendNotification = true)
    {
        // Keep backup data if we need to send a notification
        $data = $this->attributesToArray();
        // Clear data
        $this->subject_email = null;
        $this->subject_first_name = null;
        $this->subject_last_name = null;
        $this->subject_confirmed = null;
        $this->subject_confirmation_code = null;
        $this->subject_confirm_before = null;
        $this->save();

        if ($sendNotification) {
            // TODO : send email notification
        }
    }
}
