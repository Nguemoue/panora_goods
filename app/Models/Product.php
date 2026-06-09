<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
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
            ->withPivot(['value', 'specification_id', 'product_id'])
            ->withTimestamps();
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)
            ->orderByDesc('is_primary');
    }

    public function getPrimaryImageUrlAttribute(): string
    {
        if ($this->relationLoaded('primaryImage')) {
            return $this->primaryImage->url;
        }

        return asset('images/cart-placeholder.jpg');
    }

    protected function profitPrice(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => ($attributes['selling_price'] - $attributes['supplier_price']),
        );
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function getWhatsAppLink(): string
    {
        $phone = config('project_configuration.whatsapp');

        $productUrl = route('products.show', $this);

        $specifications = $this->specifications
            ->map(function ($specification) {
                return "- {$specification->name} : {$specification->pivot->value}";
            })
            ->implode("\n");

        // Construction du message
        $message = <<<TEXT
Lien du produit :
{$productUrl}

Nom du produit :
{$this->name}

Marque :
{$this->brand?->name}

Prix :
{$this->selling_price} FCFA

Référence :
{$this->reference_number}

Spécifications :
{$specifications}
TEXT;

        return 'https://wa.me/'.$phone.'?text='.urlencode($message);
    }
}
