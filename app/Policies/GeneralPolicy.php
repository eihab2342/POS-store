<?php

namespace App\Policies;

use App\Models\User;

class GeneralPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function create(User $user): bool
    {
        return $user->role === 'manager';
    }
    public function delete(User $user): bool
    {
        return $user->role === 'manager';
    }
    public function update(User $user): bool
    {
        return $user->role === 'manager';
    }

    public function viewSuppliers(User $user): bool
    {
        return $user->role === 'manager';
    }
}