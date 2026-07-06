<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SwitchAccountController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::where('id', '!=', auth()->id())->orderBy('name')->get();

        return view('switch-account', compact('restaurants'));
    }

    public function showLogin(Restaurant $restaurant)
    {
        return view('switch-account-login', compact('restaurant'));
    }

    public function switch(Request $request, Restaurant $restaurant): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('username', 'password');

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials do not match our records.'],
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Switched to '.$restaurant->name);
    }
}
