<?php

use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->restaurant = Restaurant::factory()->create();
    actingAs($this->restaurant);

    $this->menuCategory = MenuCategory::factory()->create(['restaurant_id' => $this->restaurant->id]);
});

it('index page loads successfully', function () {
    Menu::factory()->count(3)->create(['restaurant_id' => $this->restaurant->id]);

    $response = $this->get(route('menus.index'));

    $response->assertOk();
    $response->assertSee('Menus');
});

it('index page lists only own menus', function () {
    $ownMenu = Menu::factory()->create(['name' => 'Own Menu', 'restaurant_id' => $this->restaurant->id]);
    $otherRestaurant = Restaurant::factory()->create();
    Menu::factory()->create(['name' => 'Other Menu', 'restaurant_id' => $otherRestaurant->id]);

    $response = $this->get(route('menus.index'));

    $response->assertOk();
    $response->assertSee('Own Menu');
    $response->assertDontSee('Other Menu');
});

it('admin can see all menus', function () {
    $adminUser = User::factory()->create(['is_admin' => true]);
    $adminRestaurant = Restaurant::factory()->create(['user_id' => $adminUser->id]);
    $this->app['auth']->forgetGuards();
    Auth::login($adminRestaurant);

    $ownMenu = Menu::factory()->create(['name' => 'Own Menu', 'restaurant_id' => $adminRestaurant->id]);
    $otherRestaurant = Restaurant::factory()->create();
    Menu::factory()->create(['name' => 'Other Menu', 'restaurant_id' => $otherRestaurant->id]);

    $response = $this->get(route('menus.index'));

    $response->assertOk();
    $response->assertSee('Own Menu');
    $response->assertSee('Other Menu');
});

it('create page loads successfully', function () {
    $response = $this->get(route('menus.create'));

    $response->assertOk();
    $response->assertSee('Create Menu');
});

it('create page shows only own categories', function () {
    $otherRestaurant = Restaurant::factory()->create();
    $otherCategory = MenuCategory::factory()->create(['restaurant_id' => $otherRestaurant->id, 'name' => 'Other Category']);

    $response = $this->get(route('menus.create'));

    $response->assertOk();
    $response->assertDontSee('Other Category');
});

it('stores a new menu with authenticated restaurant', function () {
    $data = [
        'name' => 'Burger',
        'price' => '9.99',
        'menu_category_id' => $this->menuCategory->id,
        'description' => 'Tasty burger',
    ];

    $response = $this->post(route('menus.store'), $data);

    $response->assertRedirect(route('menus.index'));
    $this->assertDatabaseHas('menus', [
        'restaurant_id' => $this->restaurant->id,
        'name' => 'Burger',
        'slug' => 'burger',
        'price' => '9.99',
    ]);
});

it('ignores restaurant_id from input and uses authenticated restaurant', function () {
    $otherRestaurant = Restaurant::factory()->create();

    $data = [
        'restaurant_id' => $otherRestaurant->id,
        'name' => 'Hacked Menu',
        'price' => '1.00',
    ];

    $response = $this->post(route('menus.store'), $data);

    $response->assertRedirect(route('menus.index'));
    $this->assertDatabaseHas('menus', [
        'restaurant_id' => $this->restaurant->id,
        'name' => 'Hacked Menu',
    ]);
    $this->assertDatabaseMissing('menus', [
        'restaurant_id' => $otherRestaurant->id,
        'name' => 'Hacked Menu',
    ]);
});

it('validates required fields on store', function () {
    $response = $this->post(route('menus.store'), []);

    $response->assertSessionHasErrors(['name', 'price']);
});

it('edit page loads successfully', function () {
    $menu = Menu::factory()->create(['name' => 'Pizza', 'restaurant_id' => $this->restaurant->id]);

    $response = $this->get(route('menus.edit', $menu));

    $response->assertOk();
    $response->assertSee('Edit Menu');
    $response->assertSee('Pizza');
});

it('prevents editing other restaurant menu', function () {
    $otherRestaurant = Restaurant::factory()->create();
    $menu = Menu::factory()->create(['restaurant_id' => $otherRestaurant->id]);

    $response = $this->get(route('menus.edit', $menu));

    $response->assertForbidden();
});

it('admin can edit any menu', function () {
    $adminUser = User::factory()->create(['is_admin' => true]);
    $adminRestaurant = Restaurant::factory()->create(['user_id' => $adminUser->id]);
    $this->app['auth']->forgetGuards();
    Auth::login($adminRestaurant);

    $otherRestaurant = Restaurant::factory()->create();
    $menu = Menu::factory()->create(['restaurant_id' => $otherRestaurant->id]);

    $response = $this->get(route('menus.edit', $menu));

    $response->assertOk();
});

it('updates a menu', function () {
    $menu = Menu::factory()->create(['restaurant_id' => $this->restaurant->id]);

    $response = $this->put(route('menus.update', $menu), [
        'name' => 'Updated Menu',
        'price' => '19.99',
        'description' => 'Updated description',
    ]);

    $response->assertRedirect(route('menus.index'));
    $this->assertDatabaseHas('menus', [
        'id' => $menu->id,
        'restaurant_id' => $this->restaurant->id,
        'name' => 'Updated Menu',
        'slug' => 'updated-menu',
        'price' => '19.99',
    ]);
});

it('prevents updating other restaurant menu', function () {
    $otherRestaurant = Restaurant::factory()->create();
    $menu = Menu::factory()->create(['restaurant_id' => $otherRestaurant->id]);

    $response = $this->put(route('menus.update', $menu), [
        'name' => 'Hacked Update',
        'price' => '1.00',
    ]);

    $response->assertForbidden();
});

it('validates required fields on update', function () {
    $menu = Menu::factory()->create(['restaurant_id' => $this->restaurant->id]);

    $response = $this->put(route('menus.update', $menu), []);

    $response->assertSessionHasErrors(['name', 'price']);
});

it('deletes a menu', function () {
    $menu = Menu::factory()->create(['restaurant_id' => $this->restaurant->id]);

    $response = $this->delete(route('menus.destroy', $menu));

    $response->assertRedirect(route('menus.index'));
    $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
});

it('prevents deleting other restaurant menu', function () {
    $otherRestaurant = Restaurant::factory()->create();
    $menu = Menu::factory()->create(['restaurant_id' => $otherRestaurant->id]);

    $response = $this->delete(route('menus.destroy', $menu));

    $response->assertForbidden();
    $this->assertDatabaseHas('menus', ['id' => $menu->id]);
});

it('redirects unauthenticated users to login', function () {
    auth()->logout();

    $response = $this->get(route('menus.index'));

    $response->assertRedirect(route('login'));
});
