<?php

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->adminUser = User::factory()->create(['is_admin' => true]);
    $this->adminRestaurant = Restaurant::factory()->create(['user_id' => $this->adminUser->id]);
    actingAs($this->adminRestaurant);
});

it('index page loads successfully', function () {
    User::factory()->count(3)->create();

    $response = $this->get(route('users.index'));

    $response->assertOk();
    $response->assertSee('Users');
});

it('index page lists all users', function () {
    $user1 = User::factory()->create(['name' => 'John Doe']);
    $user2 = User::factory()->create(['name' => 'Jane Smith']);

    $response = $this->get(route('users.index'));

    $response->assertOk();
    $response->assertSee('John Doe');
    $response->assertSee('Jane Smith');
});

it('create page loads successfully', function () {
    $response = $this->get(route('users.create'));

    $response->assertOk();
    $response->assertSee('Create User');
});

it('stores a new user', function () {
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'is_admin' => false,
    ];

    $response = $this->post(route('users.store'), $data);

    $response->assertRedirect(route('users.index'));
    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'is_admin' => false,
    ]);
});

it('stores an admin user', function () {
    $data = [
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => 'password123',
        'is_admin' => true,
    ];

    $response = $this->post(route('users.store'), $data);

    $response->assertRedirect(route('users.index'));
    $this->assertDatabaseHas('users', [
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'is_admin' => true,
    ]);
});

it('validates required fields on store', function () {
    $response = $this->post(route('users.store'), []);

    $response->assertSessionHasErrors(['name', 'email', 'password']);
});

it('validates unique email on store', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $response = $this->post(route('users.store'), [
        'name' => 'Test',
        'email' => 'existing@example.com',
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email']);
});

it('validates password min length on store', function () {
    $response = $this->post(route('users.store'), [
        'name' => 'Test',
        'email' => 'test@example.com',
        'password' => 'short',
    ]);

    $response->assertSessionHasErrors(['password']);
});

it('edit page loads successfully', function () {
    $user = User::factory()->create(['name' => 'Edit Me']);

    $response = $this->get(route('users.edit', $user));

    $response->assertOk();
    $response->assertSee('Edit User');
    $response->assertSee('Edit Me');
});

it('updates a user', function () {
    $user = User::factory()->create();

    $response = $this->put(route('users.update', $user), [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'is_admin' => true,
    ]);

    $response->assertRedirect(route('users.index'));
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'is_admin' => true,
    ]);
});

it('updates password when provided', function () {
    $user = User::factory()->create();

    $this->put(route('users.update', $user), [
        'name' => $user->name,
        'email' => $user->email,
        'password' => 'newpassword123',
    ]);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
    ]);

    $user->refresh();
    expect(Hash::check('newpassword123', $user->password))->toBeTrue();
});

it('does not change password when blank', function () {
    $user = User::factory()->create();
    $originalPassword = $user->password;

    $this->put(route('users.update', $user), [
        'name' => 'Updated Name',
        'email' => $user->email,
        'password' => '',
    ]);

    $user->refresh();
    expect($user->password)->toBe($originalPassword);
});

it('validates required fields on update', function () {
    $user = User::factory()->create();

    $response = $this->put(route('users.update', $user), []);

    $response->assertSessionHasErrors(['name', 'email']);
});

it('validates unique email on update excluding self', function () {
    $user = User::factory()->create();
    User::factory()->create(['email' => 'taken@example.com']);

    $response = $this->put(route('users.update', $user), [
        'name' => $user->name,
        'email' => 'taken@example.com',
    ]);

    $response->assertSessionHasErrors(['email']);
});

it('allows same email on update', function () {
    $user = User::factory()->create(['email' => 'same@example.com']);

    $response = $this->put(route('users.update', $user), [
        'name' => 'Updated',
        'email' => 'same@example.com',
    ]);

    $response->assertRedirect(route('users.index'));
});

it('deletes a user', function () {
    $user = User::factory()->create();

    $response = $this->delete(route('users.destroy', $user));

    $response->assertRedirect(route('users.index'));
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

it('non-admin cannot access user management', function () {
    $this->app['auth']->forgetGuards();
    $nonAdminRestaurant = Restaurant::factory()->create();
    Auth::login($nonAdminRestaurant);

    $response = $this->get(route('users.index'));

    $response->assertForbidden();
});

it('non-admin cannot create user', function () {
    $this->app['auth']->forgetGuards();
    $nonAdminRestaurant = Restaurant::factory()->create();
    Auth::login($nonAdminRestaurant);

    $response = $this->get(route('users.create'));

    $response->assertForbidden();
});

it('non-admin cannot edit user', function () {
    $this->app['auth']->forgetGuards();
    $nonAdminRestaurant = Restaurant::factory()->create();
    Auth::login($nonAdminRestaurant);

    $user = User::factory()->create();
    $response = $this->get(route('users.edit', $user));

    $response->assertForbidden();
});

it('non-admin cannot delete user', function () {
    $this->app['auth']->forgetGuards();
    $nonAdminRestaurant = Restaurant::factory()->create();
    Auth::login($nonAdminRestaurant);

    $user = User::factory()->create();
    $response = $this->delete(route('users.destroy', $user));

    $response->assertForbidden();
    $this->assertDatabaseHas('users', ['id' => $user->id]);
});

it('redirects unauthenticated users to login', function () {
    auth()->logout();

    $response = $this->get(route('users.index'));

    $response->assertRedirect(route('login'));
});
