<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CartQuantityControlsTest extends TestCase
{
    use DatabaseMigrations;

    public function test_customer_can_increase_and_decrease_cart_item_quantity(): void
    {
        $customer = User::factory()->create();
        $cartItem = $this->createCartItem($customer, 2);

        $this->actingAs($customer)
            ->post(route('cart.items.increase', $cartItem))
            ->assertRedirect(route('cart'));

        $this->assertSame(3, $cartItem->fresh()->quantity);
        $this->get(route('cart'))
            ->assertOk()
            ->assertSee('>3</span>', false)
            ->assertSee('Rp 300.000');

        $this->post(route('cart.items.decrease', $cartItem))
            ->assertRedirect(route('cart'));

        $this->assertSame(2, $cartItem->fresh()->quantity);
    }

    public function test_quantity_is_clamped_between_one_and_ninety_nine(): void
    {
        $customer = User::factory()->create();
        $minimumItem = $this->createCartItem($customer, 1);
        $maximumItem = $this->createCartItem($customer, 99);

        $this->actingAs($customer)
            ->post(route('cart.items.decrease', $minimumItem))
            ->assertRedirect(route('cart'));

        $this->post(route('cart.items.increase', $maximumItem))
            ->assertRedirect(route('cart'));

        $this->assertSame(1, $minimumItem->fresh()->quantity);
        $this->assertSame(99, $maximumItem->fresh()->quantity);
    }

    public function test_customer_cannot_change_another_customers_cart_item(): void
    {
        $owner = User::factory()->create();
        $anotherCustomer = User::factory()->create();
        $cartItem = $this->createCartItem($owner, 2);

        $this->actingAs($anotherCustomer)
            ->post(route('cart.items.increase', $cartItem))
            ->assertNotFound();

        $this->assertSame(2, $cartItem->fresh()->quantity);
    }

    public function test_customer_can_remove_a_cart_item(): void
    {
        $customer = User::factory()->create();
        $cartItem = $this->createCartItem($customer, 2);

        $this->actingAs($customer)
            ->delete(route('cart.items.remove', $cartItem))
            ->assertRedirect(route('cart'));

        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
    }

    private function createCartItem(User $customer, int $quantity): CartItem
    {
        $category = Category::query()->create([
            'name' => 'Test category',
            'slug' => 'test-category-'.uniqid(),
        ]);
        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Test product',
            'slug' => 'test-product-'.uniqid(),
            'description' => 'Product used to test cart quantity controls.',
            'price' => 100000,
            'stock' => 20,
            'is_active' => true,
        ]);
        $cart = Cart::query()->create(['user_id' => $customer->id]);

        return CartItem::query()->create([
            'cart_id' => $cart->id,
            'user_id' => $customer->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'is_preorder' => false,
        ]);
    }
}
