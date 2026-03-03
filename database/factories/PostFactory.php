<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $status = fake()->randomElement(['published', 'published', 'published', 'draft']);
        $title = fake()->unique()->sentence(fake()->numberBetween(4, 7));
        $slugBase = Str::slug($title);
        $slugBase = $slugBase === '' ? 'post' : $slugBase;
        $slug = $slugBase.'-'.fake()->unique()->numberBetween(1000, 999999);

        $paragraphs = collect(fake()->paragraphs(fake()->numberBetween(4, 8)))
            ->map(fn (string $paragraph) => '<p>'.$paragraph.'</p>')
            ->implode("\n\n");

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => $slug,
            'excerpt' => fake()->sentence(20),
            'featured_image_path' => null,
            'body' => $paragraphs,
            'status' => $status,
            'published_at' => $status === 'published'
                ? fake()->dateTimeBetween('-18 months', 'now')
                : null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => 'published',
            'published_at' => fake()->dateTimeBetween('-18 months', 'now'),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }
}
