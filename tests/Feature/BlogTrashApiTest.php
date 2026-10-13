<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTrashApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeBlog(string $title, string $description = 'some text'): Blog
    {
        return Blog::create([
            'email' => uniqid() . '@example.com',
            'title' => $title,
            'description' => $description,
            'image_id' => Image::create(['path' => 'images/test.jpg'])->id,
            'category_id' => Category::firstOrCreate(['name' => 'News'])->id,
        ]);
    }

    public function test_search_matches_title_or_description(): void
    {
        $this->makeBlog('laravel tips');
        $this->makeBlog('cooking', 'a laravel cake');
        $this->makeBlog('travel');

        $this->getJson('/api/searchBlogs?search=laravel')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_search_does_not_return_trashed_blogs(): void
    {
        $this->makeBlog('laravel tips')->delete();

        $this->getJson('/api/searchBlogs?search=laravel')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_trash_restore_and_force_delete(): void
    {
        $blog = $this->makeBlog('old post');
        $blog->delete();

        $this->getJson('/api/trashedBlogs')->assertOk()->assertJsonCount(1);

        $this->patchJson("/api/restoreBlog/{$blog->id}")->assertOk();
        $this->assertNotSoftDeleted('blogs', ['id' => $blog->id]);
        $this->patchJson("/api/restoreBlog/{$blog->id}")->assertNotFound();

        $this->deleteJson("/api/forceDeleteBlog/{$blog->id}")->assertOk();
        $this->assertDatabaseMissing('blogs', ['id' => $blog->id]);
        $this->deleteJson("/api/forceDeleteBlog/{$blog->id}")->assertNotFound();
    }
}
