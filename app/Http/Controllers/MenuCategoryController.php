<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuCategoryRequest;
use App\Http\Requests\UpdateMenuCategoryRequest;
use App\Models\MenuCategory;
use App\Traits\RestaurantScoped;

class MenuCategoryController extends Controller
{
    use RestaurantScoped;

    public function index()
    {
        $menuCategories = MenuCategory::forRestaurant()
            ->latest()
            ->get();

        return view('menu-categories.index', compact('menuCategories'));
    }

    public function create()
    {
        return view('menu-categories.create');
    }

    public function store(StoreMenuCategoryRequest $request)
    {
        MenuCategory::create($request->validated() + [
            'restaurant_id' => auth()->id(),
        ]);

        return redirect()->route('menu-categories.index')->with('success', 'Menu category created successfully.');
    }

    public function edit(MenuCategory $menuCategory)
    {
        return view('menu-categories.edit', compact('menuCategory'));
    }

    public function update(UpdateMenuCategoryRequest $request, MenuCategory $menuCategory)
    {
        $menuCategory->update($request->validated());

        return redirect()->route('menu-categories.index')->with('success', 'Menu category updated successfully.');
    }

    public function destroy(MenuCategory $menuCategory)
    {
        $menuCategory->delete();
        return redirect()->route('menu-categories.index')->with('success', 'Menu category deleted successfully.');
    }
}
