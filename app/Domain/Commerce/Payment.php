<?php

namespace App\Domain\Commerce;

<<<<<<< HEAD
use Database\Factories\PaymentFactory;
=======
>>>>>>> origin/main
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

<<<<<<< HEAD
    protected static function newFactory(): PaymentFactory
    {
        return PaymentFactory::new();
    }

    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_FAILED = 'failed';

    public const GATEWAY_STRIPE = 'stripe';

    public const GATEWAY_GOPAY = 'gopay';

=======
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';

    public const GATEWAY_STRIPE = 'stripe';
    public const GATEWAY_GOPAY = 'gopay';
>>>>>>> origin/main
    public const GATEWAY_COMGATE = 'comgate';

    protected $fillable = [
        'order_id',
        'gateway',
        'transaction_id',
        'status',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeByGateway($query, string $gateway)
    {
        return $query->where('gateway', $gateway);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function markAsPaid(): void
    {
        $this->update(['status' => self::STATUS_PAID]);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => self::STATUS_FAILED]);
    }
}
