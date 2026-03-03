<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DemoContentSeeder extends Seeder
{
    private const DEFAULT_POST_COUNT = 50;
    private const DEFAULT_PAGE_COUNT = 10;
    private const DEFAULT_AUTHOR_COUNT = 6;

    public function run(): void
    {
        $adminUser = User::query()->firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
                'is_admin' => true,
            ]
        );

        if ($adminUser->is_admin !== true) {
            $adminUser->update(['is_admin' => true]);
        }

        $authors = User::factory(self::DEFAULT_AUTHOR_COUNT)->create();
        $authorIds = $authors->pluck('id')->push($adminUser->id)->all();

        $categories = $this->seedCategories();
        $tags = $this->seedTags();

        $this->seedPages();

        $posts = Post::factory(self::DEFAULT_POST_COUNT)
            ->state(fn () => ['user_id' => fake()->randomElement($authorIds)])
            ->create();

        $posts->each(function (Post $post) use ($categories, $tags): void {
            $post->categories()->sync(
                $this->randomModelIds($categories, 1, 3)
            );

            $post->tags()->sync(
                $this->randomModelIds($tags, 2, 5)
            );
        });
    }

    /**
     * @return Collection<int, Category>
     */
    private function seedCategories(): Collection
    {
        $names = [
            'Engineering',
            'Product',
            'Company News',
            'Guides',
            'Announcements',
            'Design',
            'Customer Stories',
            'DevOps',
        ];

        return collect($names)->map(function (string $name): Category {
            return Category::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        });
    }

    /**
     * @return Collection<int, Tag>
     */
    private function seedTags(): Collection
    {
        $names = [
            'Laravel',
            'PHP',
            'CMS',
            'SEO',
            'Performance',
            'Security',
            'API',
            'UX',
            'Roadmap',
            'Release',
            'Tutorial',
            'Accessibility',
            'Content Strategy',
            'Automation',
        ];

        return collect($names)->map(function (string $name): Tag {
            return Tag::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        });
    }

    private function seedPages(): void
    {
        $fixedPages = [
            ['title' => 'About Us', 'slug' => 'about-us'],
            ['title' => 'Contact', 'slug' => 'contact'],
            ['title' => 'Our Services', 'slug' => 'our-services'],
            ['title' => 'Pricing', 'slug' => 'pricing'],
        ];

        foreach ($fixedPages as $pageData) {
            Page::query()->updateOrCreate(
                ['slug' => $pageData['slug']],
                [
                    'title' => $pageData['title'],
                    'body' => $this->pageBodyFor($pageData['title']),
                    'status' => 'published',
                    'published_at' => now()->subDays(fake()->numberBetween(15, 180)),
                ]
            );
        }

        $remainingPages = max(0, self::DEFAULT_PAGE_COUNT - count($fixedPages));

        if ($remainingPages > 0) {
            Page::factory($remainingPages)->create();
        }
    }

    private function pageBodyFor(string $title): string
    {
        $paragraphs = collect(fake()->paragraphs(4))
            ->map(fn (string $paragraph) => '<p>'.$paragraph.'</p>')
            ->implode("\n\n");

        return '<h2>'.$title.'</h2>'."\n\n".$paragraphs;
    }

    /**
     * @param Collection<int, Category|Tag> $models
     * @return array<int, int>
     */
    private function randomModelIds(Collection $models, int $min, int $max): array
    {
        $count = fake()->numberBetween($min, min($max, $models->count()));

        return $models
            ->shuffle()
            ->take($count)
            ->pluck('id')
            ->all();
    }
}
