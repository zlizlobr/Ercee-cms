<?php

namespace App\Domain\Subscriber;

use Modules\Commerce\Domain\Order;
use Modules\Forms\Domain\Contract;
use Modules\Funnel\Domain\FunnelRun;
use Database\Factories\SubscriberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * Subscriber aggregate representing a contact across funnels, forms, and commerce.
 */
class Subscriber extends Model
{
    use HasFactory;

    /**
     * @return SubscriberFactory
     */
    protected static function newFactory(): SubscriberFactory
    {
        return SubscriberFactory::new();
    }

    protected $fillable = [
        'email',
        'status',
        'source',
    ];

    /**
     * @return HasMany<Contract>
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * @return HasMany<FunnelRun>
     */
    public function funnelRuns(): HasMany
    {
        return $this->hasMany(FunnelRun::class);
    }

    /**
     * @return HasMany<Order>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return array<int, string>
     */
    public function tags(): array
    {
        return DB::table('subscriber_tags')
            ->where('subscriber_id', $this->id)
            ->pluck('tag')
            ->toArray();
    }

    /**
     * Adds a tag to the subscriber if not already present.
     */
    public function addTag(string $tag): void
    {
        DB::table('subscriber_tags')->insertOrIgnore([
            'subscriber_id' => $this->id,
            'tag' => $tag,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Removes one tag association from the subscriber.
     */
    public function removeTag(string $tag): void
    {
        DB::table('subscriber_tags')
            ->where('subscriber_id', $this->id)
            ->where('tag', $tag)
            ->delete();
    }

    /**
     * Returns true when the subscriber has the specified tag.
     */
    public function hasTag(string $tag): bool
    {
        return DB::table('subscriber_tags')
            ->where('subscriber_id', $this->id)
            ->where('tag', $tag)
            ->exists();
    }
}
