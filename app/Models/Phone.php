<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Phone extends Model
{
    protected $fillable = [
        'brand_id',
        'supplier_id',
        'name',
        'model_number',
        'supplier_price',
        'selling_price',
        'stock_quantity',
        'ram',
        'storage',
        'color',
        'specs',
        'status',
    ];

    protected $casts = [
        'specs' => 'json',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PhoneImage::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
