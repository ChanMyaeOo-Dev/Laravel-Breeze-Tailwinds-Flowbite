<x-layouts.public :title="'Order Confirmation'" :$restaurantName="$restaurantTable->restaurant->name" :$tableNumber="$restaurantTable->table_number">
    <div class="text-center py-8">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Order Placed!</h2>
        <p class="text-gray-500 mb-6">Your order has been sent to the kitchen.</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Order #{{ $order->order_number }}</h3>
            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">
                {{ ucfirst($order->status) }}
            </span>
        </div>

        <div class="space-y-3">
            @foreach ($order->orderItems as $item)
                <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                    <div>
                        <p class="font-medium text-gray-900">{{ $item->menu->name }}</p>
                        <p class="text-sm text-gray-500">Qty: {{ $item->quantity }} x {{ number_format($item->unit_price, 2) }}</p>
                    </div>
                    <span class="font-medium text-gray-900">{{ number_format($item->subtotal, 2) }}</span>
                </div>
            @endforeach
        </div>

        <div class="mt-4 pt-4 border-t border-gray-200 space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Subtotal</span>
                <span class="text-gray-900">{{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Tax (10%)</span>
                <span class="text-gray-900">{{ number_format($order->tax_amount, 2) }}</span>
            </div>
            <div class="flex justify-between text-base font-bold pt-2 border-t border-gray-200">
                <span class="text-gray-900">Total</span>
                <span class="text-blue-600">{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>
    </div>

    <div class="text-center">
        <a href="{{ route('public.order.form', $restaurantTable->qr_code) }}"
            class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
            Place Another Order
        </a>
    </div>
</x-layouts.public>
