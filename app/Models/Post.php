<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'featured_image_path',
        'comments_enabled',
        'body',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'comments_enabled' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

protected static function booted(): void
{
    static::creating(function (Post $post) {
        if (blank($post->slug)) {
            $post->slug = static::uniqueSlugFor($post->title);
        } else {
            $post->slug = static::uniqueSlugFor($post->slug);
        }
    });

    static::updating(function (Post $post) {
        // Only regenerate if user explicitly sets slug to empty OR changes it.
        if ($post->isDirty('slug')) {
            if (blank($post->slug)) {
                $post->slug = static::uniqueSlugFor($post->title, $post->id);
            } else {
                $post->slug = static::uniqueSlugFor($post->slug, $post->id);
            }
        }
    });
}

protected static function uniqueSlugFor(string $value, ?int $ignoreId = null): string
{
    $base = Str::slug($value);

    // Fallback if title/slug becomes empty after slugging (e.g. only symbols)
    if ($base === '') {
        $base = 'post';
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

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where('published_at', '<=', now());
    }
}
