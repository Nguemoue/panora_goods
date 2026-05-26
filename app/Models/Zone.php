<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Guarded([])]
class Zone extends Model
{
    public function user(): BelongsTo // the admin of zone
    {
        return $this->belongsTo(User::class);
    }
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_zone', 'zone_id', 'user_id');
    }
}
