<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Models\Feedback;
use App\Models\Order;
use App\Models\RestaurantTable;

class PublicFeedbackController extends Controller
{
    public function store(StoreFeedbackRequest $request, string $uuid)
    {
        $restaurantTable = RestaurantTable::where('qr_code', $uuid)->firstOrFail();

        $order = Order::where('restaurant_id', $restaurantTable->restaurant_id)
            ->where('table_id', $restaurantTable->id)
            ->latest()
            ->firstOrFail();

        if (! in_array($order->status, ['served', 'completed'], true)) {
            return back()->withErrors(['feedback' => 'You can leave feedback once your order has been served.']);
        }

        if (Feedback::where('order_id', $order->id)->exists()) {
            return back()->with('feedback_success', true);
        }

        Feedback::create([
            'restaurant_id' => $restaurantTable->restaurant_id,
            'order_id' => $order->id,
            'rating' => $request->validated('rating'),
            'comment' => $request->validated('comment'),
        ]);

        return back()->with('feedback_success', true);
    }
}
