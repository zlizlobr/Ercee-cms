<?php

namespace App\Domain\Funnel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FunnelRunStep extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_SUCCESS = 'success';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'funnel_run_id',
        'funnel_step_id',
        'status',
        'executed_at',
        'payload',
        'error_message',
    ];

    protected $casts = [
        'executed_at' => 'datetime',
        'payload' => 'array',
    ];

    public function funnelRun(): BelongsTo
    {
        return $this->belongsTo(FunnelRun::class);
    }

    public function funnelStep(): BelongsTo
    {
        return $this->belongsTo(FunnelStep::class);
    }

    public function markAsSuccess(array $payload = []): void
    {
        $this->update([
            'status' => self::STATUS_SUCCESS,
            'executed_at' => now(),
            'payload' => $payload,
        ]);
    }

    public function markAsFailed(string $errorMessage, array $payload = []): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'executed_at' => now(),
            'error_message' => $errorMessage,
            'payload' => $payload,
        ]);
    }
}
