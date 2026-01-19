<?php

namespace App\Domain\Form;

use App\Domain\Subscriber\Subscriber;
use Database\Factories\ContractFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contract extends Model
{
    use HasFactory;

    protected static function newFactory(): ContractFactory
    {
        return ContractFactory::new();
    }

    public const STATUS_NEW = 'new';

    public const STATUS_QUALIFIED = 'qualified';

    public const STATUS_CONVERTED = 'converted';

    public const STATUSES = [
        self::STATUS_NEW,
        self::STATUS_QUALIFIED,
        self::STATUS_CONVERTED,
    ];

    protected $fillable = [
        'subscriber_id',
        'form_id',
        'email',
        'data',
        'source',
        'status',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeNew($query)
    {
        return $query->byStatus(self::STATUS_NEW);
    }

    public function scopeQualified($query)
    {
        return $query->byStatus(self::STATUS_QUALIFIED);
    }

    public function scopeConverted($query)
    {
        return $query->byStatus(self::STATUS_CONVERTED);
    }
}
