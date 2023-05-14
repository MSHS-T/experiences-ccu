<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

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
        'honored',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'             => 'integer',
        'confirmed'      => 'boolean',
        'confirm_before' => 'datetime',
        'honored'        => 'boolean',
    ];

    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class);
    }
}
