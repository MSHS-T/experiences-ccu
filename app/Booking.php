<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
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
        'first_name',
        'last_name',
        'email',
        'confirmed',
        'confirmation_code',
        'confirm_before',
        'honored'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'confirm_before'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->created_at = Carbon::now();
        });
    }

    public function slot()
    {
        return $this->belongsTo('App\Slot');
    }
}
