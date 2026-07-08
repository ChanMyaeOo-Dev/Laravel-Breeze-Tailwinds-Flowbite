<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class KitchenAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $restaurant = Restaurant::where('username', $request->username)->first();

        if (! $restaurant || ! Hash::check($request->password, $restaurant->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials do not match our records.'],
            ]);
        }

        if (! $restaurant->is_active) {
            throw ValidationException::withMessages([
                'username' => ['This restaurant account is inactive.'],
            ]);
        }

        $token = $restaurant->createToken('kitchen-display');

        return response()->json([
            'restaurant' => [
                'id' => $restaurant->id,
                'name' => $restaurant->name,
            ],
            'token' => $token->plainTextToken,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }
}
