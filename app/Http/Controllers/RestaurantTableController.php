<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRestaurantTableRequest;
use App\Http\Requests\UpdateRestaurantTableRequest;
use App\Models\RestaurantTable;
use App\Traits\RestaurantScoped;

class RestaurantTableController extends Controller
{
    use RestaurantScoped;

    public function index()
    {
        $restaurantTables = RestaurantTable::forRestaurant()
            ->latest()
            ->get();

        return view('restaurant-tables.index', compact('restaurantTables'));
    }

    public function create()
    {
        return view('restaurant-tables.create');
    }

    public function store(StoreRestaurantTableRequest $request)
    {
        RestaurantTable::create($request->validated() + [
            'restaurant_id' => auth()->id(),
        ]);

        return redirect()->route('restaurant-tables.index')->with('success', 'Table created successfully.');
    }

    public function edit(RestaurantTable $restaurantTable)
    {
        if (! self::isAdmin() && ! $restaurantTable->belongsToCurrentRestaurant()) {
            abort(403);
        }

        return view('restaurant-tables.edit', compact('restaurantTable'));
    }

    public function update(UpdateRestaurantTableRequest $request, RestaurantTable $restaurantTable)
    {
        $restaurantTable->update($request->validated());

        return redirect()->route('restaurant-tables.index')->with('success', 'Table updated successfully.');
    }

    public function destroy(RestaurantTable $restaurantTable)
    {
        if (! self::isAdmin() && ! $restaurantTable->belongsToCurrentRestaurant()) {
            abort(403);
        }

        $restaurantTable->delete();

        return redirect()->route('restaurant-tables.index')->with('success', 'Table deleted successfully.');
    }
}
