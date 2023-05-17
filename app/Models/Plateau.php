<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * App\Models\Plateau
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $manager_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Equipment> $equipments
 * @property-read int|null $equipments_count
 * @property-read \App\Models\User $manager
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Manipulation> $manipulations
 * @property-read int|null $manipulations_count
 * @method static \Database\Factories\PlateauFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Plateau newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plateau newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plateau query()
 * @method static \Illuminate\Database\Eloquent\Builder|Plateau whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plateau whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plateau whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plateau whereManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plateau whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plateau whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Plateau extends Model implements HasMedia
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
        'description',
        'manager_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
        'manager_id' => 'integer',
    ];

    public function equipments(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class)->withPivot('quantity');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id', 'id');
    }

    public function manipulations(): HasMany
    {
        return $this->hasMany(Manipulation::class);
    }
}
