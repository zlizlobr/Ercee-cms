<?php

namespace App\Domain\Funnel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FunnelStep extends Model
{
    use HasFactory;

    public const TYPE_DELAY = 'delay';
    public const TYPE_EMAIL = 'email';
    public const TYPE_WEBHOOK = 'webhook';
    public const TYPE_TAG = 'tag';

    protected $fillable = [
        'funnel_id',
        'type',
        'config',
        'position',
    ];

    protected $casts = [
        'config' => 'array',
        'position' => 'integer',
    ];

    public function funnel(): BelongsTo
    {
        return $this->belongsTo(Funnel::class);
    }

    public function runSteps(): HasMany
    {
        return $this->hasMany(FunnelRunStep::class);
    }

    public static function getTypes(): array
    {
        return [
            self::TYPE_DELAY => 'Delay',
            self::TYPE_EMAIL => 'Email',
            self::TYPE_WEBHOOK => 'Webhook',
            self::TYPE_TAG => 'Tag',
        ];
    }
}
