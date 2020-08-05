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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hashed_email'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'booking_made'                => 0,
        'booking_confirmed'           => 0,
        'booking_confirmed_honored'   => 0,
        'booking_unconfirmed_honored' => 0,
        'blocked'                     => false,
    ];

    public static function findByEmailOrFail(string $email): ?BookingHistory
    {
        $hashed_email = md5($email);
        return parent::where('hashed_email', $hashed_email)->firstOrFail();
    }

    public static function findByEmailOrCreate(string $email): ?BookingHistory
    {
        $hashed_email = md5($email);
        return parent::firstOrCreate(['hashed_email' => $hashed_email]);
    }
}
