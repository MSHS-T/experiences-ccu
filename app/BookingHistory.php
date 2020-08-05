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
        'crypted_email'
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
        'booking_unconfirmed_honored' => 0
    ];

    public static function findByEmailOrCreate(string $email): ?BookingHistory
    {
        $encrypted_email = Crypt::encrypt($email);
        return parent::firstOrCreate(['crypted_email' => $encrypted_email]);
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['crypted_email'] = Crypt::encrypt($value);
    }
}
