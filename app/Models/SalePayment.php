<?php

namespace App\Models;

use App\Enums\ConfirmationStatusEnum;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Guarded([])]
class SalePayment extends Model
{
    protected $guarded = [];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    protected function casts(): array
    {
        return [
            'confirmation_status' => ConfirmationStatusEnum::class
        ];
    }
}
