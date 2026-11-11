<?php

namespace Tests\Feature;

use App\Livewire\AddToCartButton;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Livewire\Livewire;
use Tests\TestCase;

class GuestAddToCartRedirectTest extends TestCase
{
    use DatabaseMigrations;

    public function test_guest_returns_to_the_product_after_logging_in_from_add_to_cart(): void
    {
        $category = Category::query()->create([
            'name' => 'Shirts',
            'slug' => 'shirts',
        ]);
        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Redline Denim Shirt',
            'slug' => 'redline-denim-shirt',
            'description' => 'Denim shirt',
            'price' => 177000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $loginUrl = route('login', ['return_product' => $product->slug]);

        Livewire::test(AddToCartButton::class, ['product' => $product])
            ->call('addToCart')
            ->assertRedirect($loginUrl);

        $this->assertSame(
            route('product.show', $product->slug),
            session('url.intended')
        );

        $this->get($loginUrl)->assertOk();

        $user = User::factory()->create([
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
            'return_product' => $product->slug,
        ])->assertRedirect(route('product.show', $product->slug));
    }
}
