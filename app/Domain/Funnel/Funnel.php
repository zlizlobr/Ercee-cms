<?php

namespace App\Domain\Funnel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Funnel extends Model
{
    use HasFactory;

    public const TRIGGER_CONTRACT_CREATED = 'contract_created';

    public const TRIGGER_ORDER_PAID = 'order_paid';

    public const TRIGGER_MANUAL = 'manual';

    protected $fillable = [
        'name',
        'trigger_type',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function steps(): HasMany
    {
        return $this->hasMany(FunnelStep::class)->orderBy('position');
    }

    public function runs(): HasMany
    {
        return $this->hasMany(FunnelRun::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeByTrigger($query, string $triggerType)
    {
        return $query->where('trigger_type', $triggerType);
    }
}
