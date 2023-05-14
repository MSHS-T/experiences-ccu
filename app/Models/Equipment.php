<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Equipment extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'description',
        'quantity',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'       => 'integer',
        'quantity' => 'integer',
    ];

    public function plateaux(): BelongsToMany
    {
        return $this->belongsToMany(Plateau::class)->withPivot('quantity');
    }
}
