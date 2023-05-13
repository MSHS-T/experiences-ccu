<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'id' => 'integer',
        'slot_count' => 'integer',
        'booking_made' => 'integer',
        'booking_confirmed' => 'integer',
        'booking_confirmed_honored' => 'integer',
        'booking_unconfirmed_honored' => 'integer',
    ];

    public function manipulation(): BelongsTo
    {
        return $this->belongsTo(Manipulation::class);
    }
}
