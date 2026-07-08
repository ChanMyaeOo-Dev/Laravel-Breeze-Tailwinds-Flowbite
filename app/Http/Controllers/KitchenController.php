<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class KitchenController extends Controller
{
    public function loginForm()
    {
        if ($this->hasValidToken()) {
            return redirect()->route('kitchen.display');
        }

        return view('kitchen.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $restaurant = Restaurant::where('username', $request->username)->first();

        if (! $restaurant || ! Hash::check($request->password, $restaurant->password)) {
            return back()->withErrors([
                'username' => 'The provided credentials do not match our records.',
            ])->onlyInput('username');
        }

        if (! $restaurant->is_active) {
            return back()->withErrors([
                'username' => 'This restaurant account is inactive.',
            ])->onlyInput('username');
        }

        Auth::guard('web')->login($restaurant);

        $token = $restaurant->createToken('kitchen-display')->plainTextToken;
        session(['kitchen_token' => $token]);

        return redirect()->route('kitchen.display');
    }

    public function display()
    {
        if (! $this->hasValidToken()) {
            return redirect()->route('kitchen.login');
        }

        $restaurant = Auth::user();
        $token = session('kitchen_token');

        return view('kitchen.display', compact('restaurant', 'token'));
    }

    public function logout()
    {
        $restaurant = Auth::user();
        if ($restaurant) {
            $restaurant->currentAccessToken()->delete();
        }

        Auth::guard('web')->logout();
        session()->forget('kitchen_token');
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('kitchen.login');
    }

    private function hasValidToken(): bool
    {
        return Auth::check() && session('kitchen_token');
    }
}
