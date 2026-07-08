<?php

namespace App\Http\Controllers;

use App\Events\OrderItemStatusUpdated;
use App\Http\Requests\StoreOrderItemRequest;
use App\Http\Requests\UpdateOrderItemRequest;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Traits\RestaurantScoped;

class OrderItemController extends Controller
{
    use RestaurantScoped;

    public function store(StoreOrderItemRequest $request, Order $order)
    {
        if (! self::isAdmin() && ! $order->belongsToCurrentRestaurant()) {
            abort(403);
        }

        $menu = Menu::where('id', $request->menu_id)
            ->where('restaurant_id', $order->restaurant_id)
            ->firstOrFail();

        $order->orderItems()->create([
            'menu_id' => $menu->id,
            'quantity' => $request->quantity,
            'unit_price' => $menu->price,
            'notes' => $request->notes ?? null,
        ]);

        $order->recalculateTotals();

        return redirect()->route('orders.show', $order)->with('success', 'Item added to order.');
    }

    public function update(UpdateOrderItemRequest $request, Order $order, OrderItem $orderItem)
    {
        if (! self::isAdmin() && ! $order->belongsToCurrentRestaurant()) {
            abort(403);
        }

        $oldStatus = $orderItem->status;
        $orderItem->update($request->validated());

        if ($orderItem->status !== $oldStatus) {
            event(new OrderItemStatusUpdated($orderItem->fresh('menu', 'order'), $oldStatus));
        }

        $order->recalculateTotals();

        return redirect()->route('orders.show', $order)->with('success', 'Order item updated.');
    }

    public function destroy(Order $order, OrderItem $orderItem)
    {
        if (! self::isAdmin() && ! $order->belongsToCurrentRestaurant()) {
            abort(403);
        }

        $orderItem->delete();
        $order->recalculateTotals();

        return redirect()->route('orders.show', $order)->with('success', 'Order item removed.');
    }
}
