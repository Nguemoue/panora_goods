<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $guarded = [];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

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
        return $this->hasMany(ProductImage::class);
    }

    public function productSpecifications(): HasMany
    {
        return $this->hasMany(ProductSpecification::class);
    }
    public function specifications(): BelongsToMany
    {
        return $this->belongsToMany(Specification::class, 'product_specification')
            ->withPivot(['value','specification_id','product_id'])
            ->withTimestamps();
    }

    protected function profitPrice(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => ($attributes['selling_price'] - $attributes['supplier_price']),
        );
    }
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
