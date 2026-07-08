<?php

use App\Events\NewOrderReceived;
use App\Events\OrderItemStatusUpdated;
use App\Events\OrderStatusUpdated;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\RestaurantTable;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->restaurant = Restaurant::factory()->create();
    $this->menuCategory = MenuCategory::factory()->create(['restaurant_id' => $this->restaurant->id]);
    $this->menu = Menu::factory()->create([
        'restaurant_id' => $this->restaurant->id,
        'menu_category_id' => $this->menuCategory->id,
        'price' => 25.00,
    ]);
    $this->restaurantTable = RestaurantTable::factory()->create(['restaurant_id' => $this->restaurant->id]);
});

describe('Kitchen Authentication', function () {
    it('can login with valid credentials', function () {
        $response = $this->postJson('/api/kitchen/login', [
            'username' => $this->restaurant->username,
            'password' => 'password',
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'restaurant' => ['id', 'name'],
            'token',
        ]);
    });

    it('cannot login with invalid credentials', function () {
        $response = $this->postJson('/api/kitchen/login', [
            'username' => $this->restaurant->username,
            'password' => 'wrong-password',
        ]);

        $response->assertUnprocessable();
    });

    it('cannot login with non-existent username', function () {
        $response = $this->postJson('/api/kitchen/login', [
            'username' => 'non-existent',
            'password' => 'password',
        ]);

        $response->assertUnprocessable();
    });

    it('can logout', function () {
        $token = $this->restaurant->createToken('kitchen-display');

        $response = $this->withToken($token->plainTextToken)
            ->postJson('/api/kitchen/logout');

        $response->assertOk();
    });
});

describe('Kitchen Orders', function () {
    beforeEach(function () {
        $this->token = $this->restaurant->createToken('kitchen-display');
    });

    it('can list active orders', function () {
        $pendingOrder = Order::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'status' => 'pending',
        ]);
        $preparingOrder = Order::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'status' => 'preparing',
        ]);
        $completedOrder = Order::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'status' => 'completed',
        ]);

        $response = $this->withToken($this->token->plainTextToken)
            ->getJson('/api/kitchen/orders');

        $response->assertOk();
        $response->assertJsonCount(2, 'orders');
    });

    it('does not include other restaurant orders', function () {
        Order::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'status' => 'pending',
        ]);
        $otherRestaurant = Restaurant::factory()->create();
        Order::factory()->create([
            'restaurant_id' => $otherRestaurant->id,
            'status' => 'pending',
        ]);

        $response = $this->withToken($this->token->plainTextToken)
            ->getJson('/api/kitchen/orders');

        $response->assertOk();
        $response->assertJsonCount(1, 'orders');
    });

    it('cannot list orders without authentication', function () {
        $response = $this->getJson('/api/kitchen/orders');

        $response->assertUnauthorized();
    });

    it('can show order details', function () {
        $order = Order::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'table_id' => $this->restaurantTable->id,
        ]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'menu_id' => $this->menu->id,
        ]);

        $response = $this->withToken($this->token->plainTextToken)
            ->getJson("/api/kitchen/orders/{$order->id}");

        $response->assertOk();
        $response->assertJsonStructure([
            'order' => [
                'id',
                'order_number',
                'status',
                'table' => ['id', 'table_number'],
                'items' => [
                    ['id', 'quantity', 'status', 'menu' => ['name']],
                ],
            ],
        ]);
    });

    it('cannot show other restaurant order', function () {
        $otherRestaurant = Restaurant::factory()->create();
        $otherOrder = Order::factory()->create([
            'restaurant_id' => $otherRestaurant->id,
        ]);

        $response = $this->withToken($this->token->plainTextToken)
            ->getJson("/api/kitchen/orders/{$otherOrder->id}");

        $response->assertForbidden();
    });

    it('can update order status', function () {
        $order = Order::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'status' => 'pending',
        ]);

        $response = $this->withToken($this->token->plainTextToken)
            ->patchJson("/api/kitchen/orders/{$order->id}/status", [
                'status' => 'preparing',
            ]);

        $response->assertOk();
        $response->assertJson(['order' => ['status' => 'preparing']]);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'preparing']);
    });

    it('broadcasts OrderStatusUpdated event', function () {
        Event::fake([OrderStatusUpdated::class]);

        $order = Order::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'status' => 'pending',
        ]);

        $this->withToken($this->token->plainTextToken)
            ->patchJson("/api/kitchen/orders/{$order->id}/status", [
                'status' => 'preparing',
            ]);

        Event::assertDispatched(OrderStatusUpdated::class, function ($event) use ($order) {
            return $event->order->id === $order->id
                && $event->oldStatus === 'pending'
                && $event->order->status === 'preparing';
        });
    });

    it('cannot update other restaurant order status', function () {
        $otherRestaurant = Restaurant::factory()->create();
        $otherOrder = Order::factory()->create([
            'restaurant_id' => $otherRestaurant->id,
            'status' => 'pending',
        ]);

        $response = $this->withToken($this->token->plainTextToken)
            ->patchJson("/api/kitchen/orders/{$otherOrder->id}/status", [
                'status' => 'preparing',
            ]);

        $response->assertForbidden();
    });

    it('validates status is required', function () {
        $order = Order::factory()->create([
            'restaurant_id' => $this->restaurant->id,
        ]);

        $response = $this->withToken($this->token->plainTextToken)
            ->patchJson("/api/kitchen/orders/{$order->id}/status", []);

        $response->assertUnprocessable();
    });

    it('validates status is from allowed values', function () {
        $order = Order::factory()->create([
            'restaurant_id' => $this->restaurant->id,
        ]);

        $response = $this->withToken($this->token->plainTextToken)
            ->patchJson("/api/kitchen/orders/{$order->id}/status", [
                'status' => 'invalid-status',
            ]);

        $response->assertUnprocessable();
    });

    it('can update order item status', function () {
        $order = Order::factory()->create([
            'restaurant_id' => $this->restaurant->id,
        ]);
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'menu_id' => $this->menu->id,
            'status' => 'pending',
        ]);

        $response = $this->withToken($this->token->plainTextToken)
            ->patchJson("/api/kitchen/orders/{$order->id}/items/{$orderItem->id}/status", [
                'status' => 'preparing',
            ]);

        $response->assertOk();
        $response->assertJson(['order_item' => ['status' => 'preparing']]);
        $this->assertDatabaseHas('order_items', ['id' => $orderItem->id, 'status' => 'preparing']);
    });

    it('broadcasts OrderItemStatusUpdated event', function () {
        Event::fake([OrderItemStatusUpdated::class]);

        $order = Order::factory()->create([
            'restaurant_id' => $this->restaurant->id,
        ]);
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'menu_id' => $this->menu->id,
            'status' => 'pending',
        ]);

        $this->withToken($this->token->plainTextToken)
            ->patchJson("/api/kitchen/orders/{$order->id}/items/{$orderItem->id}/status", [
                'status' => 'ready',
            ]);

        Event::assertDispatched(OrderItemStatusUpdated::class, function ($event) use ($orderItem) {
            return $event->orderItem->id === $orderItem->id
                && $event->oldStatus === 'pending'
                && $event->orderItem->status === 'ready';
        });
    });

    it('cannot update other restaurant order item status', function () {
        $otherRestaurant = Restaurant::factory()->create();
        $otherOrder = Order::factory()->create([
            'restaurant_id' => $otherRestaurant->id,
        ]);
        $otherItem = OrderItem::factory()->create([
            'order_id' => $otherOrder->id,
        ]);

        $response = $this->withToken($this->token->plainTextToken)
            ->patchJson("/api/kitchen/orders/{$otherOrder->id}/items/{$otherItem->id}/status", [
                'status' => 'ready',
            ]);

        $response->assertForbidden();
    });

    it('returns 404 for order item not belonging to order', function () {
        $order = Order::factory()->create([
            'restaurant_id' => $this->restaurant->id,
        ]);
        $otherOrder = Order::factory()->create([
            'restaurant_id' => $this->restaurant->id,
        ]);
        $otherItem = OrderItem::factory()->create([
            'order_id' => $otherOrder->id,
            'menu_id' => $this->menu->id,
        ]);

        $response = $this->withToken($this->token->plainTextToken)
            ->patchJson("/api/kitchen/orders/{$order->id}/items/{$otherItem->id}/status", [
                'status' => 'ready',
            ]);

        $response->assertNotFound();
    });
});

describe('New Order Event', function () {
    it('broadcasts NewOrderReceived when order is created via admin panel', function () {
        Event::fake([NewOrderReceived::class]);

        \Pest\Laravel\actingAs($this->restaurant);

        $response = $this->post(route('orders.store'), [
            'table_id' => $this->restaurantTable->id,
            'items' => [
                ['menu_id' => $this->menu->id, 'quantity' => 2],
            ],
        ]);

        $response->assertRedirect();
        Event::assertDispatched(NewOrderReceived::class);
    });
});
