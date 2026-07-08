<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Jobs\OptimizeImageJob;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Services\ImageService;
use App\Traits\RestaurantScoped;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    use RestaurantScoped;

    public function index()
    {
        $menus = Menu::forRestaurant()
            ->with('restaurant')
            ->latest()
            ->get();

        return view('menus.index', compact('menus'));
    }

    public function create()
    {
        $menuCategories = MenuCategory::forRestaurant()
            ->orderBy('display_order')
            ->get();

        return view('menus.create', compact('menuCategories'));
    }

    public function store(StoreMenuRequest $request, ImageService $imageService)
    {
        $validated = $request->validated();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $imageService->storeRaw($request->file('image'), 'menus');
            OptimizeImageJob::dispatch($imagePath, $imageService->getDisk());
        }

        $slug = Str::slug($request->name);
        if (Menu::where('slug', $slug)->exists()) {
            $slug .= '-'.Str::random(5);
        }

        unset($validated['image']);
        $menu = Menu::create($validated + [
            'restaurant_id' => auth()->id(),
            'slug' => $slug,
            'image' => $imagePath,
        ]);

        return redirect()->route('menus.index')->with('success', 'Menu created successfully.');
    }

    public function edit(Menu $menu)
    {
        if (! self::isAdmin() && ! $menu->belongsToCurrentRestaurant()) {
            abort(403);
        }

        $menuCategories = MenuCategory::forRestaurant()
            ->orderBy('display_order')
            ->get();

        return view('menus.edit', compact('menu', 'menuCategories'));
    }

    public function update(UpdateMenuRequest $request, Menu $menu, ImageService $imageService)
    {
        if (! self::isAdmin() && ! $menu->belongsToCurrentRestaurant()) {
            abort(403);
        }

        $validated = $request->validated();

        $imagePath = $menu->image;
        if ($request->hasFile('image')) {
            if ($menu->image) {
                $imageService->delete($menu->image);
            }

            $imagePath = $imageService->storeRaw($request->file('image'), 'menus');
            OptimizeImageJob::dispatch($imagePath, $imageService->getDisk());
        }

        $slug = Str::slug($request->name);
        if (Menu::where('slug', $slug)->where('id', '!=', $menu->id)->exists()) {
            $slug .= '-'.Str::random(5);
        }

        unset($validated['image']);
        $menu->update($validated + ['slug' => $slug, 'image' => $imagePath]);

        return redirect()->route('menus.index')->with('success', 'Menu updated successfully.');
    }

    public function destroy(Menu $menu, ImageService $imageService)
    {
        if (! self::isAdmin() && ! $menu->belongsToCurrentRestaurant()) {
            abort(403);
        }

        if ($menu->image) {
            $imageService->delete($menu->image);
        }

        $menu->delete();

        return redirect()->route('menus.index')->with('success', 'Menu deleted successfully.');
    }
}
