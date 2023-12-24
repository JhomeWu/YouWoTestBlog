<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Post;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_index_api(): void
    {
        Post::factory()->count(10)->create();
        $response = $this->get('/api/posts');

        $response->assertStatus(200);
        $response->assertJsonCount(10);
    }
}
