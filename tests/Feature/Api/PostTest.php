<?php

namespace Tests\Feature\Api;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function search_api(): void
    {
        Post::factory()->count(11)->create([
            'title' => 'test',
            'is_active' => true,
        ]);
        Post::factory()->count(5)->create([
            'title' => 'test',
            'is_active' => false,
        ]);
        Post::factory()->count(2)->create([
            'title' => 'NotTest',
            'is_active' => true,
        ]);
        $response = $this->post('/api/posts/search', [
            'whereColumns' => [
                ['title', '=', 'test'],
            ],
            'sortBy' => 'title',
            'order' => 'asc',
            'numberPerPage' => 10,
            'page' => 1,

        ]);

        $response->assertStatus(200);
        // Only active posts are returned
        $response->assertJsonCount(10, 'data');
        $response->assertJsonPath('hasNext', true);
        $response = $this->post('/api/posts/search', [
            'whereColumns' => [
                ['title', '=', 'test'],
            ],
            'sortBy' => 'title',
            'order' => 'asc',
            'numberPerPage' => 10,
            'page' => 2,
        ]);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('hasNext', false);
    }

    /**
     * @test
     */
    public function user_search_api(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user,
            ['*']
        );
        Post::factory()->count(11)->create([
            'title' => 'test',
            'is_active' => true,
            'user_id' => $user->id,
        ]);
        // This user but not active
        Post::factory()->count(5)->create([
            'title' => 'test',
            'is_active' => false,
            'user_id' => $user->id,
        ]);
        // Not this user
        Post::factory()->count(2)->create([
            'title' => 'test',
            'is_active' => true,
        ]);
        $searchParams = [
            'whereColumns' => [
                ['title', '=', 'test'],
            ],
            'sortBy' => 'title',
            'order' => 'asc',
            'numberPerPage' => 10,
            'page' => 1,
        ];
        $response = $this->post('/api/user/posts/search', $searchParams);

        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
        $response->assertJsonPath('hasNext', true);
        $response = $this->post('/api/user/posts/search', [
            ...$searchParams,
            'page' => 2,
        ]);
        $response->assertJsonCount(6, 'data');
        $response->assertJsonPath('hasNext', false);
    }

    /**
     * @test
     */
    public function store_api(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user,
            ['*']
        );
        $response = $this->post('/api/user/posts', [
            'title' => 'test',
            'content' => 'test',
            'is_active' => true,
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('title', 'test');
        $response->assertJsonPath('content', 'test');
        $response->assertJsonPath('is_active', true);
        $response->assertJsonPath('user_id', $user->id);
    }

    /**
     * @test
     */
    public function update_api(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user,
            ['*']
        );
        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);
        $response = $this->put("/api/user/posts/{$post->id}", [
            'title' => 'test',
            'content' => 'test',
            'is_active' => true,
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('title', 'test');
        $response->assertJsonPath('content', 'test');
        $response->assertJsonPath('is_active', true);
        $response->assertJsonPath('user_id', $user->id);
    }

    /**
     * @test
     */
    public function destroy_api(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user,
            ['*']
        );
        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);
        $response = $this->delete("/api/user/posts/{$post->id}");
        $response->assertStatus(200);
        $response->assertJsonPath('title', $post->title);
        $response->assertJsonPath('content', $post->content);
        $response->assertJsonPath('is_active', (int) $post->is_active);
        $response->assertJsonPath('user_id', $post->user_id);
    }
}
