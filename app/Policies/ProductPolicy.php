<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use App\Enums\UserRoleEnum;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [UserRoleEnum::ADMIN, UserRoleEnum::SELLER]);
    }

    public function view(User $user, Product $product): bool
    {
        return in_array($user->role, [UserRoleEnum::ADMIN, UserRoleEnum::SELLER]);
    }

    public function create(User $user): bool
    {
        return $user->role === UserRoleEnum::ADMIN;
    }

    public function update(User $user, Product $product): bool
    {
        return $user->role === UserRoleEnum::ADMIN;
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->role === UserRoleEnum::ADMIN;
    }

    public function viewSupplierPrice(User $user, Product $product): bool
    {
        return $user->role === UserRoleEnum::ADMIN;
    }
}
