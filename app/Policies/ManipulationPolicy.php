<?php

namespace App\Policies;

use App\Models\Manipulation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ManipulationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('manipulation.list');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return $user->can('manipulation.list');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('manipulation.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Manipulation $manipulation): bool
    {
        return $user->hasRole('administrator')
            || ($user->hasRole('plateau_manager') && $manipulation->plateau->manager->id === $user->id)
            || ($user->can('manipulation.edit') && $manipulation->users->pluck('id')->contains($user->id));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Manipulation $manipulation): bool
    {
        return $user->can('manipulation.delete') && !$manipulation->published;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Manipulation $manipulation): bool
    {
        return $user->can('manipulation.delete') && !$manipulation->published;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Manipulation $manipulation): bool
    {
        return $user->can('manipulation.delete') && !$manipulation->published;
    }

    /**
     * Determine whether the user can toggle the model publication status.
     */
    public function publish(User $user, Manipulation $manipulation): bool
    {
        return $user->hasRole('administrator')
            || ($user->can('manipulation.publish') && $manipulation->plateau->manager_id === $user->id);
    }

    /**
     * Determine whether the user can archive delete the model.
     */
    public function archive(User $user, Manipulation $manipulation): bool
    {
        return $user->can('manipulation.archive') && $manipulation->end_date->isAfter(now());
    }
}
