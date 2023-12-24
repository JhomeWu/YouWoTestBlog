<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Post;
use App\Models\User;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_api(): void
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
                ['title' , '=', 'test',]
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
                ['title' , '=', 'test',]
            ],
            'sortBy' => 'title',
            'order' => 'asc',
            'numberPerPage' => 10,
            'page' => 2,
        ]);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('hasNext', false);
    }

    public function test_user_search_api(): void
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
                ['title' , '=', 'test',]
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
}
