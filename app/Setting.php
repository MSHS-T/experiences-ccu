<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'name';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

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
    protected $fillable = ['name', 'value'];

    /**
     * Get all of the models from the database.
     *
     * @param  array|mixed  $columns
     * @return array()
     */
    public static function all($columns = ['*'])
    {
        return parent::all()->mapWithKeys(function ($item) {
            return [$item['name'] => $item['value']];
        })->toArray();
    }

    public static function get($name)
    {
        $setting = parent::findOrFail($name);
        return $setting->value;
    }
}
