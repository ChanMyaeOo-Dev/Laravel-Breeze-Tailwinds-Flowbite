<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderItemRequest;
use App\Http\Requests\UpdateOrderItemRequest;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;

class OrderItemController extends Controller
{
    public function store(StoreOrderItemRequest $request, Order $order)
    {
        $menu = Menu::findOrFail($request->menu_id);

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
        $orderItem->update($request->validated());

        $order->recalculateTotals();

        return redirect()->route('orders.show', $order)->with('success', 'Order item updated.');
    }

    public function destroy(Order $order, OrderItem $orderItem)
    {
        $orderItem->delete();

        $order->recalculateTotals();

        return redirect()->route('orders.show', $order)->with('success', 'Order item removed.');
    }
}
