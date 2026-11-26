<?php

declare(strict_types=1);

namespace App\Filament\Seller\Resources\Clients\Pages;

use App\Filament\Seller\Resources\Clients\ClientResource;
use Filament\Resources\Pages\ListRecords;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;
}
