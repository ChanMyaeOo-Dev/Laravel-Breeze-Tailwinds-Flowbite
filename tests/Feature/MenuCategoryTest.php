<?php

use App\Models\MenuCategory;
use App\Models\Restaurant;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->restaurant = Restaurant::factory()->create();
    actingAs($this->restaurant);
});

it('index page loads successfully', function () {
    MenuCategory::factory()->count(3)->create();

    $response = $this->get(route('menu-categories.index'));

    $response->assertOk();
    $response->assertSee('Menu Categories');
});

it('index page lists menu categories', function () {
    $category = MenuCategory::factory()->create(['name' => 'Appetizers']);

    $response = $this->get(route('menu-categories.index'));

    $response->assertOk();
    $response->assertSee('Appetizers');
});

it('create page loads successfully', function () {
    $response = $this->get(route('menu-categories.create'));

    $response->assertOk();
    $response->assertSee('Create Menu Category');
});

it('stores a new menu category', function () {
    $data = [
        'restaurant_id' => $this->restaurant->id,
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

it('validates required fields on store', function () {
    $response = $this->post(route('menu-categories.store'), []);

    $response->assertSessionHasErrors(['restaurant_id', 'name', 'display_order']);
});

it('edit page loads successfully', function () {
    $category = MenuCategory::factory()->create(['name' => 'Desserts']);

    $response = $this->get(route('menu-categories.edit', $category));

    $response->assertOk();
    $response->assertSee('Edit Menu Category');
    $response->assertSee('Desserts');
});

it('updates a menu category', function () {
    $category = MenuCategory::factory()->create();

    $response = $this->put(route('menu-categories.update', $category), [
        'restaurant_id' => $this->restaurant->id,
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

it('validates required fields on update', function () {
    $category = MenuCategory::factory()->create();

    $response = $this->put(route('menu-categories.update', $category), []);

    $response->assertSessionHasErrors(['restaurant_id', 'name', 'display_order']);
});

it('deletes a menu category', function () {
    $category = MenuCategory::factory()->create();

    $response = $this->delete(route('menu-categories.destroy', $category));

    $response->assertRedirect(route('menu-categories.index'));
    $this->assertDatabaseMissing('menu_categories', ['id' => $category->id]);
});

it('redirects unauthenticated users to login', function () {
    auth()->logout();

    $response = $this->get(route('menu-categories.index'));

    $response->assertRedirect(route('login'));
});
