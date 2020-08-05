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

    public function manipulation()
    {
        return $this->belongsTo('App\Manipulation', 'manipulation_id');
    }
}
