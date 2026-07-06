<?php

use App\Models\MenuCategory;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->restaurant = Restaurant::factory()->create();
    actingAs($this->restaurant);
});

it('index page loads successfully', function () {
    MenuCategory::factory()->count(3)->create(['restaurant_id' => $this->restaurant->id]);

    $response = $this->get(route('menu-categories.index'));

    $response->assertOk();
    $response->assertSee('Menu Categories');
});

it('index page lists only own menu categories', function () {
    $ownCategory = MenuCategory::factory()->create(['name' => 'Own Category', 'restaurant_id' => $this->restaurant->id]);
    $otherRestaurant = Restaurant::factory()->create();
    MenuCategory::factory()->create(['name' => 'Other Category', 'restaurant_id' => $otherRestaurant->id]);

    $response = $this->get(route('menu-categories.index'));

    $response->assertOk();
    $response->assertSee('Own Category');
    $response->assertDontSee('Other Category');
});

it('admin can see all menu categories', function () {
    $adminUser = User::factory()->create(['is_admin' => true]);
    $adminRestaurant = Restaurant::factory()->create(['user_id' => $adminUser->id]);
    $this->app['auth']->forgetGuards();
    Auth::login($adminRestaurant);

    $ownCategory = MenuCategory::factory()->create(['name' => 'Own Category', 'restaurant_id' => $adminRestaurant->id]);
    $otherRestaurant = Restaurant::factory()->create();
    MenuCategory::factory()->create(['name' => 'Other Category', 'restaurant_id' => $otherRestaurant->id]);

    $response = $this->get(route('menu-categories.index'));

    $response->assertOk();
    $response->assertSee('Own Category');
    $response->assertSee('Other Category');
});

it('create page loads successfully', function () {
    $response = $this->get(route('menu-categories.create'));

    $response->assertOk();
    $response->assertSee('Create Menu Category');
});

it('stores a new menu category with authenticated restaurant', function () {
    $data = [
        'name' => 'Main Course',
        'description' => 'Delicious main courses',
        'display_order' => 1,
    ];

    $response = $this->post(route('menu-categories.store'), $data);

    $response->assertRedirect(route('menu-categories.index'));
    $this->assertDatabaseHas('menu_categories', [
        'restaurant_id' => $this->restaurant->id,
        'name' => 'Main Course',
        'description' => 'Delicious main courses',
        'display_order' => 1,
    ]);
});

it('ignores restaurant_id from input and uses authenticated restaurant', function () {
    $otherRestaurant = Restaurant::factory()->create();

    $data = [
        'restaurant_id' => $otherRestaurant->id,
        'name' => 'Hacked Category',
        'display_order' => 1,
    ];

    $response = $this->post(route('menu-categories.store'), $data);

    $response->assertRedirect(route('menu-categories.index'));
    $this->assertDatabaseHas('menu_categories', [
        'restaurant_id' => $this->restaurant->id,
        'name' => 'Hacked Category',
    ]);
    $this->assertDatabaseMissing('menu_categories', [
        'restaurant_id' => $otherRestaurant->id,
        'name' => 'Hacked Category',
    ]);
});

it('validates required fields on store', function () {
    $response = $this->post(route('menu-categories.store'), []);

    $response->assertSessionHasErrors(['name', 'display_order']);
});

it('edit page loads successfully', function () {
    $category = MenuCategory::factory()->create(['name' => 'Desserts', 'restaurant_id' => $this->restaurant->id]);

    $response = $this->get(route('menu-categories.edit', $category));

    $response->assertOk();
    $response->assertSee('Edit Menu Category');
    $response->assertSee('Desserts');
});

it('prevents editing other restaurant category', function () {
    $otherRestaurant = Restaurant::factory()->create();
    $category = MenuCategory::factory()->create(['restaurant_id' => $otherRestaurant->id]);

    $response = $this->get(route('menu-categories.edit', $category));

    $response->assertForbidden();
});

it('admin can edit any category', function () {
    $adminUser = User::factory()->create(['is_admin' => true]);
    $adminRestaurant = Restaurant::factory()->create(['user_id' => $adminUser->id]);
    $this->app['auth']->forgetGuards();
    Auth::login($adminRestaurant);

    $otherRestaurant = Restaurant::factory()->create();
    $category = MenuCategory::factory()->create(['restaurant_id' => $otherRestaurant->id]);

    $response = $this->get(route('menu-categories.edit', $category));

    $response->assertOk();
});

it('updates a menu category', function () {
    $category = MenuCategory::factory()->create(['restaurant_id' => $this->restaurant->id]);

    $response = $this->put(route('menu-categories.update', $category), [
        'name' => 'Updated Category',
        'description' => 'Updated description',
        'display_order' => 5,
    ]);

    $response->assertRedirect(route('menu-categories.index'));
    $this->assertDatabaseHas('menu_categories', [
        'id' => $category->id,
        'restaurant_id' => $this->restaurant->id,
        'name' => 'Updated Category',
        'description' => 'Updated description',
        'display_order' => 5,
    ]);
});

it('prevents updating other restaurant category', function () {
    $otherRestaurant = Restaurant::factory()->create();
    $category = MenuCategory::factory()->create(['restaurant_id' => $otherRestaurant->id]);

    $response = $this->put(route('menu-categories.update', $category), [
        'name' => 'Hacked Update',
        'display_order' => 1,
    ]);

    $response->assertForbidden();
});

it('validates required fields on update', function () {
    $category = MenuCategory::factory()->create(['restaurant_id' => $this->restaurant->id]);

    $response = $this->put(route('menu-categories.update', $category), []);

    $response->assertSessionHasErrors(['name', 'display_order']);
});

it('deletes a menu category', function () {
    $category = MenuCategory::factory()->create(['restaurant_id' => $this->restaurant->id]);

    $response = $this->delete(route('menu-categories.destroy', $category));

    $response->assertRedirect(route('menu-categories.index'));
    $this->assertDatabaseMissing('menu_categories', ['id' => $category->id]);
});

it('prevents deleting other restaurant category', function () {
    $otherRestaurant = Restaurant::factory()->create();
    $category = MenuCategory::factory()->create(['restaurant_id' => $otherRestaurant->id]);

    $response = $this->delete(route('menu-categories.destroy', $category));

    $response->assertForbidden();
    $this->assertDatabaseHas('menu_categories', ['id' => $category->id]);
});

it('redirects unauthenticated users to login', function () {
    auth()->logout();

    $response = $this->get(route('menu-categories.index'));

    $response->assertRedirect(route('login'));
});
