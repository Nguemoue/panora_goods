<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;
use App\Enums\UserRoleEnum;
use Illuminate\Auth\Access\Response;

class SalePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [UserRoleEnum::ADMIN, UserRoleEnum::SELLER]);
    }

    public function view(User $user, Sale $sale): bool
    {
        if ($user->role === UserRoleEnum::ADMIN) {
            return true;
        }

        return $user->role === UserRoleEnum::SELLER && $sale->seller_id === $user->id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [UserRoleEnum::ADMIN, UserRoleEnum::SELLER]);
    }

    public function update(User $user, Sale $sale): bool
    {
        return $user->role === UserRoleEnum::ADMIN;
    }

    public function delete(User $user, Sale $sale): bool
    {
        return $user->role === UserRoleEnum::ADMIN;
    }
}
