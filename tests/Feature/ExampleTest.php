<?php

use App\Models\Restaurant;

it('returns a successful response', function () {
    $user = Restaurant::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertStatus(200);
});
