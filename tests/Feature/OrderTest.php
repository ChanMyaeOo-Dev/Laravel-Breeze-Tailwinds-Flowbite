<?php

use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->restaurant = Restaurant::factory()->create();
    actingAs($this->restaurant);

    $this->menuCategory = MenuCategory::factory()->create(['restaurant_id' => $this->restaurant->id]);
    $this->menu = Menu::factory()->create([
        'restaurant_id' => $this->restaurant->id,
        'menu_category_id' => $this->menuCategory->id,
        'price' => 25.00,
    ]);
});

it('index page loads successfully', function () {
    Order::factory()->count(3)->create(['restaurant_id' => $this->restaurant->id]);

    $response = $this->get(route('orders.index'));

    $response->assertOk();
    $response->assertSee('Orders');
});

it('index page lists orders', function () {
    $order = Order::factory()->create([
        'restaurant_id' => $this->restaurant->id,
        'order_number' => 'ORD-TEST001',
    ]);

    $response = $this->get(route('orders.index'));

    $response->assertOk();
    $response->assertSee('ORD-TEST001');
});

it('create page loads successfully', function () {
    $response = $this->get(route('orders.create'));

    $response->assertOk();
    $response->assertSee('Create Order');
});

it('stores a new order with items', function () {
    $data = [
        'special_instructions' => 'No onions',
        'items' => [
            ['menu_id' => $this->menu->id, 'quantity' => 2, 'notes' => 'Extra spicy'],
        ],
    ];

    $response = $this->post(route('orders.store'), $data);

    $response->assertRedirect(route('orders.index'));
    $this->assertDatabaseHas('orders', [
        'restaurant_id' => $this->restaurant->id,
        'special_instructions' => 'No onions',
    ]);

    $order = Order::where('restaurant_id', $this->restaurant->id)->first();
    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->id,
        'menu_id' => $this->menu->id,
        'quantity' => 2,
        'unit_price' => 25.00,
    ]);
});

it('validates required fields on store', function () {
    $response = $this->post(route('orders.store'), []);

    $response->assertSessionHasErrors(['items']);
});

it('validates items array on store', function () {
    $response = $this->post(route('orders.store'), [
        'items' => [],
    ]);

    $response->assertSessionHasErrors(['items']);
});

it('show page loads successfully', function () {
    $order = Order::factory()->create(['restaurant_id' => $this->restaurant->id]);
    OrderItem::factory()->create(['order_id' => $order->id, 'menu_id' => $this->menu->id]);

    $response = $this->get(route('orders.show', $order));

    $response->assertOk();
    $response->assertSee($order->order_number);
});

it('edit page loads successfully', function () {
    $order = Order::factory()->create(['restaurant_id' => $this->restaurant->id]);

    $response = $this->get(route('orders.edit', $order));

    $response->assertOk();
    $response->assertSee('Edit Order');
});

it('updates an order', function () {
    $order = Order::factory()->create(['restaurant_id' => $this->restaurant->id]);

    $response = $this->put(route('orders.update', $order), [
        'status' => 'preparing',
        'special_instructions' => 'Updated instructions',
    ]);

    $response->assertRedirect(route('orders.index'));
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'preparing',
        'special_instructions' => 'Updated instructions',
    ]);
});

it('validates status on update', function () {
    $order = Order::factory()->create(['restaurant_id' => $this->restaurant->id]);

    $response = $this->put(route('orders.update', $order), [
        'status' => 'invalid_status',
    ]);

    $response->assertSessionHasErrors(['status']);
});

it('deletes an order', function () {
    $order = Order::factory()->create(['restaurant_id' => $this->restaurant->id]);

    $response = $this->delete(route('orders.destroy', $order));

    $response->assertRedirect(route('orders.index'));
    $this->assertDatabaseMissing('orders', ['id' => $order->id]);
});

it('adds item to order', function () {
    $order = Order::factory()->create(['restaurant_id' => $this->restaurant->id]);

    $response = $this->post(route('orders.order-items.store', $order), [
        'menu_id' => $this->menu->id,
        'quantity' => 3,
        'notes' => 'Extra sauce',
    ]);

    $response->assertRedirect(route('orders.show', $order));
    $this->assertDatabaseHas('order_items', [
        'order_id' => $order->id,
        'menu_id' => $this->menu->id,
        'quantity' => 3,
        'unit_price' => 25.00,
    ]);
});

it('validates required fields on store order item', function () {
    $order = Order::factory()->create(['restaurant_id' => $this->restaurant->id]);

    $response = $this->post(route('orders.order-items.store', $order), []);

    $response->assertSessionHasErrors(['menu_id', 'quantity']);
});

it('updates order item', function () {
    $order = Order::factory()->create(['restaurant_id' => $this->restaurant->id]);
    $item = OrderItem::factory()->create(['order_id' => $order->id, 'menu_id' => $this->menu->id]);

    $response = $this->put(route('orders.order-items.update', [$order, $item]), [
        'quantity' => 5,
    ]);

    $response->assertRedirect(route('orders.show', $order));
    $this->assertDatabaseHas('order_items', [
        'id' => $item->id,
        'quantity' => 5,
    ]);
});

it('removes item from order', function () {
    $order = Order::factory()->create(['restaurant_id' => $this->restaurant->id]);
    $item = OrderItem::factory()->create(['order_id' => $order->id, 'menu_id' => $this->menu->id]);

    $response = $this->delete(route('orders.order-items.destroy', [$order, $item]));

    $response->assertRedirect(route('orders.show', $order));
    $this->assertDatabaseMissing('order_items', ['id' => $item->id]);
});

it('recalculates totals when item is added', function () {
    $order = Order::factory()->create([
        'restaurant_id' => $this->restaurant->id,
        'subtotal' => 0,
        'tax_amount' => 0,
        'total_amount' => 0,
    ]);

    $this->post(route('orders.order-items.store', $order), [
        'menu_id' => $this->menu->id,
        'quantity' => 2,
    ]);

    $order->refresh();
    expect((float) $order->subtotal)->toBe(50.00);
    expect((float) $order->tax_amount)->toBe(5.00);
    expect((float) $order->total_amount)->toBe(55.00);
});

it('redirects unauthenticated users to login', function () {
    auth()->logout();

    $response = $this->get(route('orders.index'));

    $response->assertRedirect(route('login'));
});
