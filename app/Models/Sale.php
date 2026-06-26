<?php

namespace App\Models;

use App\Enums\ConfirmationStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\SaleStatusEnum;
use App\Settings\PaymentSetting;
use Database\Factories\SaleFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Sale extends Model
{
    /** @use HasFactory<SaleFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'sold_at' => 'datetime',
        'status' => SaleStatusEnum::class,
        'payment_status' => PaymentStatusEnum::class,
        'payment_type' => PaymentTypeEnum::class,
        'payment_date_limit' => 'date',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(static function ($sale) {
            if (empty($sale->tracking_code)) {
                $sale->tracking_code = self::generateUniqueTrackingCode();
            }
        });
    }

    #[Scope]
    protected function delivered(Builder $query): Builder
    {
        return $query->where('status', SaleStatusEnum::DELIVERED);
    }

    #[Scope]
    protected function notDelivered(Builder $query): Builder
    {
        return $query->whereNot('status', SaleStatusEnum::DELIVERED);
    }

    #[Scope]
    protected function paymentFinished(Builder $query): Builder
    {
        return $query->where('payment_status', PaymentStatusEnum::PAID);
    }

    #[Scope]
    protected function paymentPending(Builder $query): Builder
    {
        return $query->whereNot('payment_status', PaymentStatusEnum::PAID);
    }

    #[Scope]
    protected function overduePayment(Builder $query): Builder
    {
        return $query
            ->whereNot('payment_status', PaymentStatusEnum::PAID)
            ->whereNotNull('payment_date_limit')
            ->whereDate('payment_date_limit', '<', today());
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
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function salePayments(): HasMany
    {
        return $this->hasMany(SalePayment::class);
    }

    public function approvedSalePayments(): HasMany
    {
        return $this->hasMany(SalePayment::class)
            ->where('confirmation_status', ConfirmationStatusEnum::APPROVED);
    }

    public function isOneTimePayment(): bool
    {
        return $this->payment_type === PaymentTypeEnum::ONE_TIME;
    }

    public function getApprovedPaidAmount(): float
    {
        if (array_key_exists('paid_amount', $this->attributes)) {
            return (float) $this->attributes['paid_amount'];
        }

        return (float) $this->approvedSalePayments()->sum('amount');
    }

    public function getRemainingAmount(): float
    {
        return max(0, (float) $this->sale_price - $this->getApprovedPaidAmount());
    }

    protected function sellerProfit(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => app(PaymentSetting::class)->seller_rate * $attributes['profit'],
        );
    }
}
