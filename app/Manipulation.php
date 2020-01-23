<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Manipulation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'plateau_id',
        'duration',
        'target_slots',
        'start_date',
        'location',
        'requirements',
        'available_hours'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'available_hours' => 'array',
        'requirements'    => 'array',
    ];

    /**
     * The relationships that should always be included
     */
    protected $with = ['managers'];

    public function plateau(){
        return $this->belongsTo('App\Plateau', 'plateau_id');
    }

    public function managers(){
        return $this->belongsToMany('App\User');
    }
}
