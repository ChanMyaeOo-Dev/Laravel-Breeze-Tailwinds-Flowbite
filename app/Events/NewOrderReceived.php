<?php

namespace App\Events;

use App\Http\Resources\KitchenOrderResource;
use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Order $order,
    ) {
        $this->order->load('orderItems.menu', 'table');
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('restaurant.'.$this->order->restaurant_id.'.kitchen'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.received';
    }

    public function broadcastWith(): array
    {
        return [
            'order' => (new KitchenOrderResource($this->order))->resolve(),
        ];
    }
}
