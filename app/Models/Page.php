<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'body',
        'status',
        'published_at',
    ];

    protected static function booted(): void
{
    static::creating(function (Page $page) {
        if (blank($page->slug)) {
            $page->slug = static::uniqueSlugFor($page->title);
        } else {
            $page->slug = static::uniqueSlugFor($page->slug);
        }
    });

    static::updating(function (Page $page) {
        if ($page->isDirty('slug')) {
            if (blank($page->slug)) {
                $page->slug = static::uniqueSlugFor($page->title, $page->id);
            } else {
                $page->slug = static::uniqueSlugFor($page->slug, $page->id);
            }
        }
    });
}

protected static function uniqueSlugFor(string $value, ?int $ignoreId = null): string
{
    $base = Str::slug($value);

    if ($base === '') {
        $base = 'page';
    }

    $slug = $base;
    $i = 2;

    while (static::query()
        ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
        ->where('slug', $slug)
        ->exists()
    ) {
        $slug = "{$base}-{$i}";
        $i++;
    }

    return $slug;
}
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where('published_at', '<=', now());
    }
}