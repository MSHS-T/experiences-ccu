<?php

namespace App\Policies;

use App\Models\Plateau;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PlateauPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('plateau.list');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Plateau $plateau): bool
    {
        return $user->can('plateau.list');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('plateau.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Plateau $plateau): bool
    {
        return $user->hasRole('administrator')
            || ($user->can('plateau.edit') && $plateau->manager_id === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Plateau $plateau): bool
    {
        return $user->can('plateau.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Plateau $plateau): bool
    {
        return $user->can('plateau.delete');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Plateau $plateau): bool
    {
        return $user->can('plateau.delete');
    }
}
