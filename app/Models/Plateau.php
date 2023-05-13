<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Plateau extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'manager_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'manager_id' => 'integer',
    ];

    public function equipment(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class)->withPivot('quantity');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
