<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Jobs\OptimizeImageJob;
use App\Models\Menu;
use App\Services\ImageService;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::latest()->with('restaurant')->get();

        return view('menus.index', compact('menus'));
    }

    public function create()
    {
        return view('menus.create');
    }

    public function store(StoreMenuRequest $request, ImageService $imageService)
    {
        $validated = $request->validated();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $imageService->storeRaw($request->file('image'), 'menus');
            OptimizeImageJob::dispatch($imagePath, $imageService->getDisk());
        }

        unset($validated['image']);
        $menu = Menu::create($validated + ['image' => $imagePath]);

        return redirect()->route('menus.index')->with('success', 'Menu created successfully.');
    }

    public function edit(Menu $menu)
    {
        return view('menus.edit', compact('menu'));
    }

    public function update(UpdateMenuRequest $request, Menu $menu, ImageService $imageService)
    {
        $validated = $request->validated();

        $imagePath = $menu->image;
        if ($request->hasFile('image')) {
            if ($menu->image) {
                $imageService->delete($menu->image);
            }

            $imagePath = $imageService->storeRaw($request->file('image'), 'menus');
            OptimizeImageJob::dispatch($imagePath, $imageService->getDisk());
        }

        unset($validated['image']);
        $menu->update($validated + ['image' => $imagePath]);

        return redirect()->route('menus.index')->with('success', 'Menu updated successfully.');
    }

    public function destroy(Menu $menu, ImageService $imageService)
    {
        if ($menu->image) {
            $imageService->delete($menu->image);
        }

        $menu->delete();

        return redirect()->route('menus.index')->with('success', 'Menu deleted successfully.');
    }
}
