<?php

namespace App\Policies;

use App\Models\ProductListingIssue;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductListingIssuePolicy
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
        return $user->tokenCan('manage:products');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProductListingIssue  $productListingIssue
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ProductListingIssue $productListingIssue)
    {
        return $user->tokenCan('manage:products');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProductListingIssue  $productListingIssue
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ProductListingIssue $productListingIssue)
    {
        return $user->tokenCan('manage:products');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProductListingIssue  $productListingIssue
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ProductListingIssue $productListingIssue)
    {
        return $user->tokenCan('manage:products');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProductListingIssue  $productListingIssue
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ProductListingIssue $productListingIssue)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProductListingIssue  $productListingIssue
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ProductListingIssue $productListingIssue)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProductListingIssue  $productListingIssue
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function resolved(User $user)
    {
        return $user->tokenCan('manage:products');
    }
}
