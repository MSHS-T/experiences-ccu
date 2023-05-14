<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Slot extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'start',
        'end',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'    => 'integer',
        'start' => 'datetime',
        'end'   => 'datetime',
    ];

    public function manipulation(): BelongsTo
    {
        return $this->belongsTo(Manipulation::class);
    }

    public function booking(): HasOne
    {
        return $this->hasOne(Booking::class);
    }
}
