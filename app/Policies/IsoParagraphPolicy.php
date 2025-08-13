<?php

namespace App\Policies;

use App\Models\IsoParagraph;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class IsoParagraphPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, IsoParagraph $isoParagraph): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, IsoParagraph $isoParagraph): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, IsoParagraph $isoParagraph): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, IsoParagraph $isoParagraph): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, IsoParagraph $isoParagraph): bool
    {
        //
    }
}
