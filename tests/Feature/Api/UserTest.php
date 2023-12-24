<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function login_api(): void
    {
        $user = User::factory()->create([
            'email' => 'test',
            'password' => Hash::make('test'),
        ]);

        $response = $this->post('/api/user/login', [
            'email' => 'test',
            'password' => 'test',
        ]);
        $user = $user->fresh();
        $response->assertStatus(200);
        $response->assertJsonPath('name', $user->name);
        $response->assertJsonPath('token', function ($token) use ($user) {
            // vendor/laravel/sanctum/src/PersonalAccessToken.php
            [$id, $token] = explode('|', $token, 2);

            return hash_equals($user->tokens->first()->token, hash('sha256', $token));
        });
    }
}
