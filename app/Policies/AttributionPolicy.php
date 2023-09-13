<?php

namespace App\Policies;

use App\Models\Attribution;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttributionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('attribution.list');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return $user->can('attribution.list');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('attribution.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Attribution $attribution): bool
    {
        return $user->hasRole('administrator')
            || ($user->can('attribution.edit') && $attribution->creator->id === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Attribution $attribution): bool
    {
        return $user->hasRole('administrator')
            || ($user->can('attribution.edit') && $attribution->creator->id === $user->id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return $user->can('attribution.delete');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Attribution $attribution): bool
    {
        return $user->can('attribution.delete');
    }
}
