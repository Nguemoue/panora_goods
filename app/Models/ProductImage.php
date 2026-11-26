<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ProductImageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    /** @use HasFactory<ProductImageFactory> */
    use HasFactory;

    protected $fillable = ['product_id', 'path', 'thumb_path', 'type', 'is_primary'];

    public function getUrlAttribute(): string
    {
        if (blank($this->path)) {
            return asset('images/cart-placeholder.jpg');
        }

        return asset('storage/'.ltrim((string) $this->path, '/'));
    }

    public function getThumbUrlAttribute(): string
    {
        if (filled($this->thumb_path)) {
            return asset('storage/'.ltrim((string) $this->thumb_path, '/'));
        }

        return $this->url;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
