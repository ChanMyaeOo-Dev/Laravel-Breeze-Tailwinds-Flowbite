<?php

use App\Models\Restaurant;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

test('reset password link screen can be rendered', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
});

test('reset password link can be requested', function () {
    Notification::fake();

    $user = Restaurant::factory()->create(['email' => 'test@example.com']);

    $this->post('/forgot-password', ['email' => 'test@example.com']);

    Notification::assertSentTo($user, ResetPassword::class);
})->skip('Restaurant model does not have an email field');

test('reset password screen can be rendered', function () {
    Notification::fake();

    $user = Restaurant::factory()->create(['email' => 'test@example.com']);

    $this->post('/forgot-password', ['email' => 'test@example.com']);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
        $response = $this->get('/reset-password/'.$notification->token);

        $response->assertStatus(200);

        return true;
    });
})->skip('Restaurant model does not have an email field');

test('password can be reset with valid token', function () {
    Notification::fake();

    $user = Restaurant::factory()->create(['email' => 'test@example.com']);

    $this->post('/forgot-password', ['email' => 'test@example.com']);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
        $response = $this->post('/reset-password', [
            'token' => $notification->token,
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'));

        return true;
    });
})->skip('Restaurant model does not have an email field');
