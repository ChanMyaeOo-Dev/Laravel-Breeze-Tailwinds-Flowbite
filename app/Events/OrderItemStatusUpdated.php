<?php

namespace App\Events;

use App\Models\OrderItem;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderItemStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public OrderItem $orderItem,
        public string $oldStatus,
    ) {
        $this->orderItem->load('menu', 'order');
    }

    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('restaurant.'.$this->orderItem->order->restaurant_id.'.kitchen'),
            new Channel('orders.'.$this->orderItem->order_id),
        ];

        if ($this->orderItem->order?->table_id) {
            $channels[] = new Channel('tables.'.$this->orderItem->order->table_id);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'order.item.status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->orderItem->order_id,
            'order_number' => $this->orderItem->order->order_number,
            'order_item_id' => $this->orderItem->id,
            'menu_name' => $this->orderItem->menu->name,
            'old_status' => $this->oldStatus,
            'new_status' => $this->orderItem->status,
        ];
    }
}
