<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTagApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_categories_by_name(): void
    {
        Category::create(['name' => 'Tech']);
        Category::create(['name' => 'Food']);

        $this->getJson('/api/categories')
            ->assertOk()
            ->assertJsonPath('0.name', 'Food')
            ->assertJsonPath('1.name', 'Tech');
    }

    public function test_it_lists_tags_by_name(): void
    {
        Tag::create(['name' => 'PHP', 'slug' => 'php']);
        Tag::create(['name' => 'Laravel', 'slug' => 'laravel']);

        $this->getJson('/api/tags')
            ->assertOk()
            ->assertJsonPath('0.name', 'Laravel')
            ->assertJsonPath('1.name', 'PHP');
    }
}
