<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// !!! Relates to a view

class SlotBooking extends Model
{
    use HasFactory;

    protected $table        = 'slotbooking_view';
    protected $primaryKey   = 'id';
    protected $keyType      = 'string';
    public    $incrementing = false;

    protected $fillable = [];
}
