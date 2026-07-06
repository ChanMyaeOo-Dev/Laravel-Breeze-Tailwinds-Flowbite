<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Order;
use App\Traits\RestaurantScoped;

class OrderController extends Controller
{
    use RestaurantScoped;

    public function index()
    {
        $orders = Order::forRestaurant()
            ->with('orderItems.menu')
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $menus = Menu::forRestaurant()
            ->where('status', true)
            ->orderBy('name')
            ->get();

        $menuCategories = MenuCategory::forRestaurant()
            ->with(['menus' => fn ($q) => $q->where('status', true)])
            ->orderBy('display_order')
            ->get();

        return view('orders.create', compact('menus', 'menuCategories'));
    }

    public function store(StoreOrderRequest $request)
    {
        $order = Order::create([
            'restaurant_id' => auth()->id(),
            'special_instructions' => $request->special_instructions,
        ]);

        foreach ($request->items as $item) {
            $menu = Menu::forRestaurant()
                ->findOrFail($item['menu_id']);

            $order->orderItems()->create([
                'menu_id' => $menu->id,
                'quantity' => $item['quantity'],
                'unit_price' => $menu->price,
                'notes' => $item['notes'] ?? null,
            ]);
        }

        $order->recalculateTotals();

        return redirect()->route('orders.index')->with('success', 'Order created successfully.');
    }

    public function show(Order $order)
    {
        if (! self::isAdmin() && ! $order->belongsToCurrentRestaurant()) {
            abort(403);
        }

        $order->load('orderItems.menu');

        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        if (! self::isAdmin() && ! $order->belongsToCurrentRestaurant()) {
            abort(403);
        }

        $menus = Menu::forRestaurant()
            ->where('status', true)
            ->orderBy('name')
            ->get();

        $order->load('orderItems.menu');

        return view('orders.edit', compact('order', 'menus'));
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        if (! self::isAdmin() && ! $order->belongsToCurrentRestaurant()) {
            abort(403);
        }

        $order->update($request->validated());

        return redirect()->route('orders.index')->with('success', 'Order updated successfully.');
    }

    public function destroy(Order $order)
    {
        if (! self::isAdmin() && ! $order->belongsToCurrentRestaurant()) {
            abort(403);
        }

        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }
}
