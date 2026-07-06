<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuCategoryRequest;
use App\Http\Requests\UpdateMenuCategoryRequest;
use App\Models\MenuCategory;

class MenuCategoryController extends Controller
{
    public function index()
    {
        $menuCategories = MenuCategory::latest()->get();

        return view('menu-categories.index', compact('menuCategories'));
    }

    public function create()
    {
        return view('menu-categories.create');
    }

    public function store(StoreMenuCategoryRequest $request)
    {
        MenuCategory::create($request->validated());

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
