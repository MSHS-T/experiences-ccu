<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Attribution extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plateau_id',
        'manipulation_manager_id',
        'creator_id',
        'start_date',
        'end_date',
        'allowed_halfdays',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'               => 'integer',
        'start_date'       => 'date',
        'end_date'         => 'date',
        'allowed_halfdays' => 'array',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Attribution $attribution) {
            $attribution->creator()->associate(
                App::runningInConsole()
                    ? User::role('administrator')->first()
                    : Auth::user()
            );
        });
    }

    public function plateau(): BelongsTo
    {
        return $this->belongsTo(Plateau::class, 'plateau_id', 'id');
    }

    public function manipulationManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manipulation_manager_id', 'id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function getAllowedHalfdaysDisplay(): array
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        return collect($this->allowed_halfdays)
            ->sort(function ($a, $b) use ($days) {
                $daysCmp = array_search(Str::before($a, '_'), $days) <=> array_search(Str::before($b, '_'), $days);
                if ($daysCmp != 0) {
                    return $daysCmp;
                }
                return $a <=> $b;
            })
            ->map(function ($hd) {
                [$day, $halfday] = explode('_', $hd);
                return __('attributes.' . $day) . ' ' . __('attributes.' . $halfday);
            })
            ->all();
    }
}
