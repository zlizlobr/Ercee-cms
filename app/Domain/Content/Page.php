<?php

namespace App\Domain\Content;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'content',
        'seo_meta',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'seo_meta' => 'array',
            'published_at' => 'datetime',
        ];
    }
}
