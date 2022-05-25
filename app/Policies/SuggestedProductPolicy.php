<?php

namespace App\Policies;

use App\Models\SuggestedProduct;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SuggestedProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->tokenCan('manage:suggested-products') || $user->tokenCan('add:suggested-products');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SuggestedProduct  $suggestedProduct
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, SuggestedProduct $suggestedProduct)
    {
        return $user->tokenCan('manage:suggested-products') || $suggestedProduct->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->tokenCan('manage:suggested-products') || $user->tokenCan('add:suggested-products');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SuggestedProduct  $suggestedProduct
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, SuggestedProduct $suggestedProduct)
    {
        if (!$user->tokenCan('manage:suggested-products') && $user->tokenCan('add:suggested-products')) {
            return $suggestedProduct->user_id === $user->id && $suggestedProduct->status === 'added';
        }

        return $user->tokenCan('manage:suggested-products');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SuggestedProduct  $suggestedProduct
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, SuggestedProduct $suggestedProduct)
    {
        if (!$user->tokenCan('manage:suggested-products') && $user->tokenCan('add:suggested-products')) {
            return $suggestedProduct->user_id === $user->id && $suggestedProduct->status === 'added';
        }

        return $user->tokenCan('manage:suggested-products');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SuggestedProduct  $suggestedProduct
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, SuggestedProduct $suggestedProduct)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SuggestedProduct  $suggestedProduct
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, SuggestedProduct $suggestedProduct)
    {
        return false;
    }
}
