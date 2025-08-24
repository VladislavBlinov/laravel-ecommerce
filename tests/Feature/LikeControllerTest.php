<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LikeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_liked_products()
    {
        $user = User::factory()->create();
        $products = Product::factory()->count(7)->create();
        $user->likedProducts()->attach($products->take(6));

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/likes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name']
                ],
                'links',
                'meta',
            ]);

        $this->assertCount(5, $response->json('data'));
    }

    public function test_show_returns_true_if_user_liked_product()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $user->likedProducts()->attach($product);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/likes/{$product->id}");

        $response->assertStatus(200)
            ->assertJson(['liked' => true]);
    }

    public function test_show_returns_false_if_user_did_not_like_product()
    {
        Sanctum::actingAs(User::factory()->create());
        $product = Product::factory()->create();

        $response = $this->getJson("/api/likes/{$product->id}");

        $response->assertStatus(200)
            ->assertJson(['liked' => false]);
    }

    public function test_store_toggles_like_on_product()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/likes/{$product->id}");
        $response->assertStatus(200)
            ->assertJson(['liked' => true]);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $response = $this->postJson("/api/likes/{$product->id}");
        $response->assertStatus(200)
            ->assertJson(['liked' => false]);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }
}
