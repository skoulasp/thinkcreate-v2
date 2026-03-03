<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        $status = fake()->randomElement(['published', 'published', 'published', 'draft']);
        $title = fake()->unique()->sentence(fake()->numberBetween(2, 5));
        $slugBase = Str::slug($title);
        $slugBase = $slugBase === '' ? 'page' : $slugBase;
        $slug = 'page-'.$slugBase.'-'.fake()->unique()->numberBetween(1000, 999999);

        $heading = fake()->sentence(fake()->numberBetween(3, 6));
        $intro = fake()->paragraph();
        $sections = collect(fake()->paragraphs(fake()->numberBetween(3, 6)))
            ->map(fn (string $paragraph) => '<p>'.$paragraph.'</p>')
            ->implode("\n\n");

        return [
            'title' => $title,
            'slug' => $slug,
            'body' => '<h2>'.$heading.'</h2>'."\n\n".'<p>'.$intro.'</p>'."\n\n".$sections,
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
