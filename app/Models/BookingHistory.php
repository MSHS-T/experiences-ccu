<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hashed_email',
        'booking_made',
        'booking_confirmed',
        'booking_confirmed_honored',
        'booking_unconfirmed_honored',
        'blocked',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'booking_made' => 'integer',
        'booking_confirmed' => 'integer',
        'booking_confirmed_honored' => 'integer',
        'booking_unconfirmed_honored' => 'integer',
        'blocked' => 'boolean',
    ];
}
