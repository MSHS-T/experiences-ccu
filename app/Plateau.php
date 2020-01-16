<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plateau extends Model
{
    /**
     * The table name (override plural form)
     *
     * @var string
     */
    public $table = "plateaux";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'manager_id'
    ];

    /**
     * The relationships that should always be included
     */
    protected $with = ['manager'];

    public function manager(){
        return $this->belongsTo('App\User', 'manager_id');
    }
}
