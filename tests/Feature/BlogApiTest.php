<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Image;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlogApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    private function makeBlog(array $attributes = []): Blog
    {
        $image = Image::create(['path' => 'images/test.jpg']);
        $category = Category::create(['name' => 'News ' . uniqid()]);

        return Blog::create(array_merge([
            'email' => uniqid() . '@example.com',
            'title' => 'first blog',
            'description' => 'hello world',
            'image_id' => $image->id,
            'category_id' => $category->id,
        ], $attributes));
    }

    public function test_it_lists_blogs_with_relations(): void
    {
        $this->makeBlog();

        $this->getJson('/api/blogs')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure(['data' => [['id', 'title', 'description', 'category', 'tags', 'image' => ['url']]]]);
    }

    public function test_it_shows_one_blog_or_404(): void
    {
        $blog = $this->makeBlog();

        $this->getJson("/api/blog/{$blog->id}")->assertOk()->assertJsonPath('title', 'First Blog');
        $this->getJson('/api/blog/999')->assertNotFound();
    }

    public function test_it_adds_a_blog_with_image_and_tags(): void
    {
        $category = Category::create(['name' => 'Tech']);
        $tag = Tag::create(['name' => 'Laravel', 'slug' => 'laravel']);

        $response = $this->postJson('/api/addBlog', [
            'email' => 'sarmad@example.com',
            'title' => 'my api blog',
            'description' => 'created from the api',
            'category_id' => $category->id,
            'file' => UploadedFile::fake()->image('cover.jpg'),
            'tags' => [$tag->id],
        ]);

        $response->assertOk()
            ->assertJsonPath('result', 'Data has been saved')
            ->assertJsonPath('blog.title', 'My Api Blog')
            ->assertJsonPath('blog.tags.0.name', 'Laravel');

        $path = Image::first()->path;
        Storage::disk('public')->assertExists($path);
    }

    public function test_add_blog_validates_input(): void
    {
        $this->postJson('/api/addBlog', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'title', 'description', 'category_id', 'file']);
    }

    public function test_add_blog_rejects_duplicate_email(): void
    {
        $this->makeBlog(['email' => 'taken@example.com']);

        $this->postJson('/api/addBlog', ['email' => 'taken@example.com'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_it_updates_a_blog_without_a_new_image(): void
    {
        $blog = $this->makeBlog();
        $category = Category::create(['name' => 'Sports']);

        $this->putJson('/api/updateBlog', [
            'id' => $blog->id,
            'email' => $blog->email,
            'title' => 'updated title',
            'description' => 'updated text',
            'category_id' => $category->id,
        ])->assertOk()->assertJsonPath('blog.title', 'Updated Title');

        $this->assertSame($category->id, $blog->fresh()->category_id);
    }

    public function test_update_returns_404_for_missing_blog(): void
    {
        $category = Category::create(['name' => 'Tech']);

        $this->putJson('/api/updateBlog', [
            'id' => 999,
            'email' => 'nobody@example.com',
            'title' => 'x',
            'description' => 'y',
            'category_id' => $category->id,
        ])->assertNotFound();
    }

    public function test_delete_soft_deletes_the_blog(): void
    {
        $blog = $this->makeBlog();

        $this->deleteJson("/api/deleteBlog/{$blog->id}")->assertOk();
        $this->assertSoftDeleted('blogs', ['id' => $blog->id]);

        $this->deleteJson("/api/deleteBlog/{$blog->id}")->assertNotFound();
    }
}
