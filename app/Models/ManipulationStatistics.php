<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\ManipulationStatistics
 *
 * @property int $id
 * @property int $slot_count
 * @property int $booking_made
 * @property int $booking_confirmed
 * @property int $booking_confirmed_honored
 * @property int $booking_unconfirmed_honored
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Manipulation|null $manipulation
 * @method static \Database\Factories\ManipulationStatisticsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ManipulationStatistics newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ManipulationStatistics newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ManipulationStatistics query()
 * @method static \Illuminate\Database\Eloquent\Builder|ManipulationStatistics whereBookingConfirmed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManipulationStatistics whereBookingConfirmedHonored($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManipulationStatistics whereBookingMade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManipulationStatistics whereBookingUnconfirmedHonored($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManipulationStatistics whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManipulationStatistics whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManipulationStatistics whereSlotCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManipulationStatistics whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ManipulationStatistics extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'slot_count',
        'booking_made',
        'booking_confirmed',
        'booking_confirmed_honored',
        'booking_unconfirmed_honored',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                          => 'integer',
        'slot_count'                  => 'integer',
        'booking_made'                => 'integer',
        'booking_confirmed'           => 'integer',
        'booking_confirmed_honored'   => 'integer',
        'booking_unconfirmed_honored' => 'integer',
    ];

    public function manipulation(): BelongsTo
    {
        return $this->belongsTo(Manipulation::class);
    }
}
