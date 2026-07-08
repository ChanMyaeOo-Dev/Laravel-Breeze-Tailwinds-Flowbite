<?php

namespace App\Http\Controllers\Api;

use App\Events\OrderItemStatusUpdated;
use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use App\Http\Resources\KitchenOrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KitchenOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $restaurantId = $request->user()->id;

        $orders = Order::where('restaurant_id', $restaurantId)
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->with('orderItems.menu', 'table')
            ->latest()
            ->get();

        return response()->json([
            'orders' => KitchenOrderResource::collection($orders),
        ]);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        if ($order->restaurant_id !== $request->user()->id) {
            abort(403, 'You are not authorized to view this order.');
        }

        $order->load('orderItems.menu', 'table');

        return response()->json([
            'order' => new KitchenOrderResource($order),
        ]);
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        if ($order->restaurant_id !== $request->user()->id) {
            abort(403, 'You are not authorized to update this order.');
        }

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,preparing,ready,served,completed,cancelled'],
        ]);

        $oldStatus = $order->status;
        $order->update(['status' => $validated['status']]);

        event(new OrderStatusUpdated($order, $oldStatus));

        return response()->json([
            'message' => 'Order status updated.',
            'order' => new KitchenOrderResource($order->fresh('orderItems.menu', 'table')),
        ]);
    }

    public function updateItemStatus(Request $request, Order $order, OrderItem $orderItem): JsonResponse
    {
        if ($order->restaurant_id !== $request->user()->id) {
            abort(403, 'You are not authorized to update this order item.');
        }

        if ($orderItem->order_id !== $order->id) {
            abort(404, 'Order item not found for this order.');
        }

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,preparing,ready,served,cancelled'],
        ]);

        $oldStatus = $orderItem->status;
        $orderItem->update(['status' => $validated['status']]);

        event(new OrderItemStatusUpdated($orderItem, $oldStatus));

        return response()->json([
            'message' => 'Order item status updated.',
            'order_item' => [
                'id' => $orderItem->id,
                'status' => $orderItem->fresh()->status,
            ],
        ]);
    }
}
