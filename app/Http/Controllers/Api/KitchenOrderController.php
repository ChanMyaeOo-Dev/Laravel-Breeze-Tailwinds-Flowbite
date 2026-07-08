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
use Illuminate\Validation\ValidationException;

class KitchenOrderController extends Controller
{
    private const ALLOWED_ORDER_TRANSITIONS = [
        'pending' => ['preparing', 'cancelled'],
        'preparing' => ['ready', 'cancelled'],
        'ready' => ['served'],
        'served' => [],
        'completed' => [],
        'cancelled' => [],
    ];

    private const ALLOWED_ITEM_TRANSITIONS = [
        'pending' => ['preparing', 'cancelled'],
        'preparing' => ['ready', 'cancelled'],
        'ready' => ['served'],
        'served' => [],
        'cancelled' => [],
    ];

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

        $newStatus = $validated['status'];
        $allowed = self::ALLOWED_ORDER_TRANSITIONS[$order->status] ?? [];

        if (! in_array($newStatus, $allowed)) {
            throw ValidationException::withMessages([
                'status' => "Cannot transition from \"{$order->status}\" to \"{$newStatus}\".",
            ]);
        }

        $oldStatus = $order->status;
        $order->update(['status' => $newStatus]);

        if (in_array($newStatus, ['served', 'completed', 'cancelled'])) {
            $order->orderItems()
                ->whereNotIn('status', ['cancelled'])
                ->update(['status' => $newStatus === 'cancelled' ? 'cancelled' : 'served']);
        }

        event(new OrderStatusUpdated($order->fresh('orderItems.menu', 'table'), $oldStatus));

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

        $newStatus = $validated['status'];
        $allowed = self::ALLOWED_ITEM_TRANSITIONS[$orderItem->status] ?? [];

        if (! in_array($newStatus, $allowed)) {
            throw ValidationException::withMessages([
                'status' => "Cannot transition from \"{$orderItem->status}\" to \"{$newStatus}\".",
            ]);
        }

        $oldStatus = $orderItem->status;
        $orderItem->update(['status' => $newStatus]);

        event(new OrderItemStatusUpdated($orderItem->fresh('menu', 'order'), $oldStatus));

        return response()->json([
            'message' => 'Order item status updated.',
            'order_item' => [
                'id' => $orderItem->id,
                'status' => $orderItem->fresh()->status,
            ],
        ]);
    }
}
