<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Post;
use Tests\TestCase;

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
}
