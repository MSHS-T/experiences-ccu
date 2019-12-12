<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public $fillable = ['name'];

    public $timestamps = false;
}
