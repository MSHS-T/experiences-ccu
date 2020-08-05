<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class BookingHistory extends Model
{
    /**
     * The table name (override plural form)
     *
     * @var string
     */
    public $table = "booking_history";

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public static function findByEmail(string $email): ?BookingHistory
    {
        $encrypted_email = Crypt::encrypt($email);
        return parent::where('crypted_email', $encrypted_email)->first();
    }
}
