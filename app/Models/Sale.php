<?php

namespace App\Models;

use App\Enums\SaleStatusEnum;
use Database\Factories\SaleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Sale extends Model
{
    /** @use HasFactory<SaleFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'seller_id',
        'client_id',
        'quantity',
        'supplier_price_at_sale',
        'sale_price',
        'profit',
        'customer_name',
        'tracking_code',
        'status',
        'sold_at',
    ];

    protected $casts = [
        'sold_at' => 'datetime',
        'status' => SaleStatusEnum::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            if (empty($sale->tracking_code)) {
                $sale->tracking_code = self::generateUniqueTrackingCode();
            }
        });
    }

    public static function generateUniqueTrackingCode(): string
    {
        do {
            $code = strtoupper(Str::random(10));
        } while (self::where('tracking_code', $code)->exists());

        return $code;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
