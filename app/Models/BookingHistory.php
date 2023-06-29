<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BookingHistory
 *
 * @property int $id
 * @property string $hashed_email
 * @property \Illuminate\Support\Carbon $birthdate
 * @property int $booking_made
 * @property int $booking_confirmed
 * @property int $booking_confirmed_honored
 * @property int $booking_unconfirmed_honored
 * @property bool $blocked
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\BookingHistoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|BookingHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BookingHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BookingHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|BookingHistory whereBlocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookingHistory whereBookingConfirmed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookingHistory whereBookingConfirmedHonored($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookingHistory whereBookingMade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookingHistory whereBookingUnconfirmedHonored($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookingHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookingHistory whereHashedEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookingHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookingHistory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
        'birthdate',
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
        'id'                          => 'integer',
        'birthdate'                   => 'date',
        'booking_made'                => 'integer',
        'booking_confirmed'           => 'integer',
        'booking_confirmed_honored'   => 'integer',
        'booking_unconfirmed_honored' => 'integer',
        'blocked'                     => 'boolean',
    ];
}
