<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;

/**
 * App\Models\Manipulation
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $plateau_id
 * @property int $duration
 * @property int $target_slots
 * @property \Illuminate\Support\Carbon $start_date
 * @property string $location
 * @property array $available_hours
 * @property array $requirements
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Plateau $plateau
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Slot> $slots
 * @property-read int|null $slots_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\ManipulationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation whereAvailableHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation wherePlateauId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation whereRequirements($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation whereTargetSlots($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Manipulation extends Model
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
        'duration',
        'target_slots',
        'start_date',
        'location',
        'available_hours',
        'requirements',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'              => 'integer',
        'duration'        => 'integer',
        'target_slots'    => 'integer',
        'start_date'      => 'date',
        'available_hours' => 'array',
        'requirements'    => 'array',
        'published'       => 'boolean',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'published' => false,
    ];

    public function plateau(): BelongsTo
    {
        return $this->belongsTo(Plateau::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function slots(): HasMany
    {
        return $this->hasMany(Slot::class);
    }

    public function getOpeningHoursDisplay(): string
    {
        return '';
    }

    public function togglePublished()
    {
        $this->published = !$this->published;
        $this->save();
    }
}
