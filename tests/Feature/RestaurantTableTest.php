<?php

use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Restaurant;
use App\Models\RestaurantTable;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->restaurant = Restaurant::factory()->create();
    actingAs($this->restaurant);
});

it('index page loads successfully', function () {
    RestaurantTable::factory()->count(3)->create(['restaurant_id' => $this->restaurant->id]);

    $response = $this->get(route('restaurant-tables.index'));

    $response->assertOk();
    $response->assertSee('Tables');
});

it('index page lists only own restaurant tables', function () {
    $ownTable = RestaurantTable::factory()->create(['restaurant_id' => $this->restaurant->id, 'table_number' => 'T-1']);
    $otherRestaurant = Restaurant::factory()->create();
    RestaurantTable::factory()->create(['restaurant_id' => $otherRestaurant->id, 'table_number' => 'T-99']);

    $response = $this->get(route('restaurant-tables.index'));

    $response->assertOk();
    $response->assertSee('T-1');
    $response->assertDontSee('T-99');
});

it('admin can see all restaurant tables', function () {
    $adminUser = User::factory()->create(['is_admin' => true]);
    $adminRestaurant = Restaurant::factory()->create(['user_id' => $adminUser->id]);
    $this->app['auth']->forgetGuards();
    Auth::login($adminRestaurant);

    $ownTable = RestaurantTable::factory()->create(['restaurant_id' => $adminRestaurant->id, 'table_number' => 'T-1']);
    $otherRestaurant = Restaurant::factory()->create();
    RestaurantTable::factory()->create(['restaurant_id' => $otherRestaurant->id, 'table_number' => 'T-99']);

    $response = $this->get(route('restaurant-tables.index'));

    $response->assertOk();
    $response->assertSee('T-1');
    $response->assertSee('T-99');
});

it('create page loads successfully', function () {
    $response = $this->get(route('restaurant-tables.create'));

    $response->assertOk();
    $response->assertSee('Create Table');
});

it('stores a new restaurant table', function () {
    $data = [
        'table_number' => 'T-101',
        'seating_capacity' => 4,
        'status' => 'available',
    ];

    $response = $this->post(route('restaurant-tables.store'), $data);

    $response->assertRedirect(route('restaurant-tables.index'));
    $this->assertDatabaseHas('restaurant_tables', [
        'restaurant_id' => $this->restaurant->id,
        'table_number' => 'T-101',
        'seating_capacity' => 4,
        'status' => 'available',
    ]);
});

it('auto-generates qr_code and qr_code_image on store', function () {
    $data = [
        'table_number' => 'T-102',
        'seating_capacity' => 2,
    ];

    $this->post(route('restaurant-tables.store'), $data);

    $table = RestaurantTable::where('restaurant_id', $this->restaurant->id)->where('table_number', 'T-102')->first();
    $this->assertNotEmpty($table->qr_code);
    $this->assertNotEmpty($table->qr_code_image);
    $this->assertStringStartsWith('qrcodes/', $table->qr_code_image);
});

it('validates required fields on store', function () {
    $response = $this->post(route('restaurant-tables.store'), []);

    $response->assertSessionHasErrors(['table_number', 'seating_capacity']);
});

it('validates unique table_number per restaurant on store', function () {
    RestaurantTable::factory()->create([
        'restaurant_id' => $this->restaurant->id,
        'table_number' => 'T-DUPLICATE',
    ]);

    $response = $this->post(route('restaurant-tables.store'), [
        'table_number' => 'T-DUPLICATE',
        'seating_capacity' => 4,
    ]);

    $response->assertSessionHasErrors(['table_number']);
});

it('allows same table_number in different restaurants', function () {
    $otherRestaurant = Restaurant::factory()->create();
    RestaurantTable::factory()->create([
        'restaurant_id' => $otherRestaurant->id,
        'table_number' => 'T-SHARED',
    ]);

    $response = $this->post(route('restaurant-tables.store'), [
        'table_number' => 'T-SHARED',
        'seating_capacity' => 4,
    ]);

    $response->assertRedirect(route('restaurant-tables.index'));
    $this->assertDatabaseHas('restaurant_tables', [
        'restaurant_id' => $this->restaurant->id,
        'table_number' => 'T-SHARED',
    ]);
});

it('validates status enum on store', function () {
    $response = $this->post(route('restaurant-tables.store'), [
        'table_number' => 'T-103',
        'seating_capacity' => 4,
        'status' => 'invalid_status',
    ]);

    $response->assertSessionHasErrors(['status']);
});

it('edit page loads successfully', function () {
    $table = RestaurantTable::factory()->create(['restaurant_id' => $this->restaurant->id]);

    $response = $this->get(route('restaurant-tables.edit', $table));

    $response->assertOk();
    $response->assertSee('Edit Table');
});

it('prevents editing other restaurant table', function () {
    $otherRestaurant = Restaurant::factory()->create();
    $table = RestaurantTable::factory()->create(['restaurant_id' => $otherRestaurant->id]);

    $response = $this->get(route('restaurant-tables.edit', $table));

    $response->assertForbidden();
});

it('admin can edit any restaurant table', function () {
    $adminUser = User::factory()->create(['is_admin' => true]);
    $adminRestaurant = Restaurant::factory()->create(['user_id' => $adminUser->id]);
    $this->app['auth']->forgetGuards();
    Auth::login($adminRestaurant);

    $otherRestaurant = Restaurant::factory()->create();
    $table = RestaurantTable::factory()->create(['restaurant_id' => $otherRestaurant->id]);

    $response = $this->get(route('restaurant-tables.edit', $table));

    $response->assertOk();
});

it('updates a restaurant table', function () {
    $table = RestaurantTable::factory()->create(['restaurant_id' => $this->restaurant->id]);

    $response = $this->put(route('restaurant-tables.update', $table), [
        'table_number' => 'T-UPDATED',
        'seating_capacity' => 8,
        'status' => 'occupied',
    ]);

    $response->assertRedirect(route('restaurant-tables.index'));
    $this->assertDatabaseHas('restaurant_tables', [
        'id' => $table->id,
        'table_number' => 'T-UPDATED',
        'seating_capacity' => 8,
        'status' => 'occupied',
    ]);
});

it('prevents updating other restaurant table', function () {
    $otherRestaurant = Restaurant::factory()->create();
    $table = RestaurantTable::factory()->create(['restaurant_id' => $otherRestaurant->id]);

    $response = $this->put(route('restaurant-tables.update', $table), [
        'table_number' => 'T-HACKED',
    ]);

    $response->assertForbidden();
});

it('deletes a restaurant table', function () {
    $table = RestaurantTable::factory()->create(['restaurant_id' => $this->restaurant->id]);

    $response = $this->delete(route('restaurant-tables.destroy', $table));

    $response->assertRedirect(route('restaurant-tables.index'));
    $this->assertDatabaseMissing('restaurant_tables', ['id' => $table->id]);
});

it('prevents deleting other restaurant table', function () {
    $otherRestaurant = Restaurant::factory()->create();
    $table = RestaurantTable::factory()->create(['restaurant_id' => $otherRestaurant->id]);

    $response = $this->delete(route('restaurant-tables.destroy', $table));

    $response->assertForbidden();
    $this->assertDatabaseHas('restaurant_tables', ['id' => $table->id]);
});

it('admin can delete any restaurant table', function () {
    $adminUser = User::factory()->create(['is_admin' => true]);
    $adminRestaurant = Restaurant::factory()->create(['user_id' => $adminUser->id]);
    $this->app['auth']->forgetGuards();
    Auth::login($adminRestaurant);

    $otherRestaurant = Restaurant::factory()->create();
    $table = RestaurantTable::factory()->create(['restaurant_id' => $otherRestaurant->id]);

    $response = $this->delete(route('restaurant-tables.destroy', $table));

    $response->assertRedirect(route('restaurant-tables.index'));
    $this->assertDatabaseMissing('restaurant_tables', ['id' => $table->id]);
});

it('redirects unauthenticated users to login', function () {
    auth()->logout();

    $response = $this->get(route('restaurant-tables.index'));

    $response->assertRedirect(route('login'));
});

it('public order form page loads for valid uuid', function () {
    $menuCategory = MenuCategory::factory()->create(['restaurant_id' => $this->restaurant->id]);
    Menu::factory()->create([
        'restaurant_id' => $this->restaurant->id,
        'menu_category_id' => $menuCategory->id,
        'status' => true,
    ]);
    $table = RestaurantTable::factory()->create(['restaurant_id' => $this->restaurant->id]);

    $response = $this->get(route('public.order.form', $table->qr_code));

    $response->assertOk();
    $response->assertSee($this->restaurant->name);
    $response->assertSee($table->table_number);
});

it('public order form returns 404 for invalid uuid', function () {
    $response = $this->get(route('public.order.form', 'invalid-uuid'));

    $response->assertNotFound();
});

it('public order store creates an order', function () {
    $menuCategory = MenuCategory::factory()->create(['restaurant_id' => $this->restaurant->id]);
    $menu = Menu::factory()->create([
        'restaurant_id' => $this->restaurant->id,
        'menu_category_id' => $menuCategory->id,
        'price' => 15.00,
        'status' => true,
    ]);
    $table = RestaurantTable::factory()->create(['restaurant_id' => $this->restaurant->id]);

    $data = [
        'special_instructions' => 'No spice',
        'items' => [
            ['menu_id' => $menu->id, 'quantity' => 2],
        ],
    ];

    $response = $this->post(route('public.order.store', $table->qr_code), $data);

    $response->assertRedirect(route('public.order.confirmation', $table->qr_code));
    $this->assertDatabaseHas('orders', [
        'restaurant_id' => $this->restaurant->id,
        'table_id' => $table->id,
        'special_instructions' => 'No spice',
    ]);
});
