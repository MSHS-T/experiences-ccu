<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ManipulationStatistics extends Model
{
    /**
     * The table name (override plural form)
     *
     * @var string
     */
    public $table = "manipulation_statistics";

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
        'slot_count',
        'booking_made',
        'booking_confirmed',
        'booking_confirmed_honored',
        'booking_unconfirmed_honored'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'slot_count'                  => 0,
        'booking_made'                => 0,
        'booking_confirmed'           => 0,
        'booking_confirmed_honored'   => 0,
        'booking_unconfirmed_honored' => 0
    ];

    public function manipulation()
    {
        return $this->belongsTo('App\Manipulation', 'manipulation_id');
    }
}
