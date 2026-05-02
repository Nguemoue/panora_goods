<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    case ADMIN = 'admin';
    case SELLER = 'seller';
    case CLIENT = 'client';
}
