<?php

namespace App\Domain\Subscriber;

use App\Domain\Form\Contract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
