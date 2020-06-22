<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
}
