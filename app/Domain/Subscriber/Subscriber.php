<?php

namespace App\Domain\Subscriber;

use App\Domain\Form\Contract;
use App\Domain\Funnel\FunnelRun;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'status',
        'source',
    ];

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function funnelRuns(): HasMany
    {
        return $this->hasMany(FunnelRun::class);
    }

    public function tags(): array
    {
        return DB::table('subscriber_tags')
            ->where('subscriber_id', $this->id)
            ->pluck('tag')
            ->toArray();
    }

    public function addTag(string $tag): void
    {
        DB::table('subscriber_tags')->insertOrIgnore([
            'subscriber_id' => $this->id,
            'tag' => $tag,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function removeTag(string $tag): void
    {
        DB::table('subscriber_tags')
            ->where('subscriber_id', $this->id)
            ->where('tag', $tag)
            ->delete();
    }

    public function hasTag(string $tag): bool
    {
        return DB::table('subscriber_tags')
            ->where('subscriber_id', $this->id)
            ->where('tag', $tag)
            ->exists();
    }
}
