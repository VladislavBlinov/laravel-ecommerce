<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_returns_user_data_when_auth()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => [
                        'id',
                        'name',
                        'email',
                        'email_verified_at',
                        'address',
                    ]
                ]
            );
    }

    public function test_show_returns_unauthorized_when_guest()
    {
        $this->getJson('/api/user')
            ->assertStatus(401);
    }

    public function test_update_updates_user_data()
    {
        Sanctum::actingAs(User::factory()->create());
        $payload = [
            'name' => 'New User',
            'email' => 'new@email.com',
            'address' => 'New Address',
        ];

        $response = $this->putJson('/api/user', $payload);

        $response->assertStatus(200)
            ->assertJsonFragment($payload);

        $this->assertDatabaseHas('users', $payload);
    }
}
