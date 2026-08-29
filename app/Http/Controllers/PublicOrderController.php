<?php

namespace App\Http\Controllers;

use App\Events\NewOrderReceived;
use App\Models\Feedback;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Order;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;

class PublicOrderController extends Controller
{
    public function showOrderForm(string $uuid)
    {
        $restaurantTable = RestaurantTable::where('qr_code', $uuid)
            ->with('restaurant')
            ->firstOrFail();

        $menus = Menu::where('restaurant_id', $restaurantTable->restaurant_id)
            ->where('status', true)
            ->orderBy('name')
            ->get();

        $menuCategories = MenuCategory::where('restaurant_id', $restaurantTable->restaurant_id)
            ->with(['menus' => fn ($q) => $q->where('status', true)])
            ->orderBy('display_order')
            ->get();

        $activeOrder = Order::where('restaurant_id', $restaurantTable->restaurant_id)
            ->where('table_id', $restaurantTable->id)
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->with('orderItems.menu')
            ->latest()
            ->first();

        return view('public.order', compact('restaurantTable', 'menus', 'menuCategories', 'activeOrder'));
    }

    public function storeOrder(Request $request, string $uuid)
    {
        $restaurantTable = RestaurantTable::where('qr_code', $uuid)->firstOrFail();

        $validated = $request->validate([
            'special_instructions' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string'],
        ]);

        $order = Order::create([
            'restaurant_id' => $restaurantTable->restaurant_id,
            'table_id' => $restaurantTable->id,
            'special_instructions' => $validated['special_instructions'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            $menu = Menu::where('restaurant_id', $restaurantTable->restaurant_id)
                ->where('id', $item['menu_id'])
                ->firstOrFail();

            $order->orderItems()->create([
                'menu_id' => $menu->id,
                'quantity' => $item['quantity'],
                'unit_price' => $menu->price,
                'notes' => $item['notes'] ?? null,
            ]);
        }

        $order->recalculateTotals();

        event(new NewOrderReceived($order->fresh('orderItems.menu', 'table')));

        return redirect()->route('public.order.confirmation', $uuid)
            ->with('success', 'Order placed successfully!');
    }

    public function confirmation(string $uuid)
    {
        $restaurantTable = RestaurantTable::where('qr_code', $uuid)
            ->with('restaurant')
            ->firstOrFail();

        $order = Order::where('restaurant_id', $restaurantTable->restaurant_id)
            ->where('table_id', $restaurantTable->id)
            ->latest()
            ->with('orderItems.menu')
            ->firstOrFail();

        $existingFeedback = Feedback::where('order_id', $order->id)->first();

        return view('public.confirmation', compact('restaurantTable', 'order', 'existingFeedback'));
    }
}
