<x-layouts.public :title="'Order Confirmation'" :restaurant-name="$restaurantTable->restaurant->name" :table-number="$restaurantTable->table_number">
    <div x-data="orderConfirmationTracker()" x-init="init()" class="space-y-6">
        {{-- Hero Status Banner --}}
        <div class="text-center py-6">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 transition-all duration-300"
                :class="{
                    'bg-info/10 text-info': status === 'pending',
                    'bg-brand/10 text-brand ring-4 ring-brand/20 animate-pulse': status === 'preparing',
                    'bg-success/10 text-success ring-4 ring-success/20 animate-bounce': status === 'ready',
                    'bg-success/10 text-success': status === 'served' || status === 'completed',
                    'bg-danger/10 text-danger': status === 'cancelled'
                }">
                {{-- Pending Icon --}}
                <svg x-show="status === 'pending'" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{-- Preparing Icon --}}
                <svg x-show="status === 'preparing'" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                </svg>
                {{-- Ready Icon --}}
                <svg x-show="status === 'ready'" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                {{-- Served / Completed Icon --}}
                <svg x-show="status === 'served' || status === 'completed'" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{-- Cancelled Icon --}}
                <svg x-show="status === 'cancelled'" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>

            <h2 class="text-2xl font-bold text-dark mb-2" x-text="statusHeading">Order Placed!</h2>
            <p class="text-body max-w-md mx-auto" x-text="statusMessage">Your order has been sent to the kitchen.</p>

            {{-- Real-time indicator badge --}}
            <div class="inline-flex items-center gap-1.5 mt-3 px-2.5 py-1 rounded-full text-xs bg-light border border-default text-body">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-success opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-success"></span>
                </span>
                <span>Live Kitchen Updates Active</span>
            </div>
        </div>

        {{-- Interactive Progress Tracker Card --}}
        <div class="bg-white border border-default rounded-xl p-6 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-bold text-dark">Order #{{ $order->order_number }}</h3>
                    <p class="text-xs text-body">Table {{ $restaurantTable->table_number }}</p>
                </div>
                <span class="text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wider transition-colors"
                    :class="{
                        'bg-info/10 text-info': status === 'pending',
                        'bg-brand/10 text-brand animate-pulse': status === 'preparing',
                        'bg-success/10 text-success ring-1 ring-success/30': status === 'ready',
                        'bg-light text-dark': status === 'served' || status === 'completed',
                        'bg-danger/10 text-danger': status === 'cancelled'
                    }"
                    x-text="status.toUpperCase()">
                    {{ ucfirst($order->status) }}
                </span>
            </div>

            {{-- Progress Stepper --}}
            <div class="mb-8 px-2">
                <div class="flex items-center justify-between">
                    {{-- Step 1: Received --}}
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all duration-300"
                            :class="stepIndex >= 0 ? 'bg-brand border-brand text-white shadow-sm' : 'bg-light border-default text-body'">
                            <svg x-show="stepIndex > 0" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span x-show="stepIndex <= 0">1</span>
                        </div>
                        <span class="text-xs mt-1.5 font-medium transition-colors"
                            :class="stepIndex >= 0 ? 'text-brand font-semibold' : 'text-body'">Received</span>
                    </div>

                    {{-- Connector 1-2 --}}
                    <div class="flex-1 h-0.5 mx-2 -mt-4 transition-colors duration-300"
                        :class="stepIndex >= 1 ? 'bg-brand' : 'bg-default'"></div>

                    {{-- Step 2: Preparing --}}
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all duration-300"
                            :class="stepIndex >= 1 ? (status === 'preparing' ? 'bg-brand border-brand text-white shadow-sm ring-4 ring-brand/20' : 'bg-brand border-brand text-white') : 'bg-light border-default text-body'">
                            <svg x-show="stepIndex > 1" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span x-show="stepIndex <= 1">2</span>
                        </div>
                        <span class="text-xs mt-1.5 font-medium transition-colors"
                            :class="stepIndex >= 1 ? 'text-brand font-semibold' : 'text-body'">Preparing</span>
                    </div>

                    {{-- Connector 2-3 --}}
                    <div class="flex-1 h-0.5 mx-2 -mt-4 transition-colors duration-300"
                        :class="stepIndex >= 2 ? 'bg-brand' : 'bg-default'"></div>

                    {{-- Step 3: Ready --}}
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all duration-300"
                            :class="stepIndex >= 2 ? (status === 'ready' ? 'bg-success border-success text-white shadow-sm ring-4 ring-success/20' : 'bg-brand border-brand text-white') : 'bg-light border-default text-body'">
                            <svg x-show="stepIndex > 2" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span x-show="stepIndex <= 2">3</span>
                        </div>
                        <span class="text-xs mt-1.5 font-medium transition-colors"
                            :class="stepIndex >= 2 ? 'text-success font-semibold' : 'text-body'">Ready</span>
                    </div>

                    {{-- Connector 3-4 --}}
                    <div class="flex-1 h-0.5 mx-2 -mt-4 transition-colors duration-300"
                        :class="stepIndex >= 3 ? 'bg-brand' : 'bg-default'"></div>

                    {{-- Step 4: Served --}}
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all duration-300"
                            :class="stepIndex >= 3 ? 'bg-brand border-brand text-white shadow-sm' : 'bg-light border-default text-body'">
                            <svg x-show="stepIndex >= 3" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span x-show="stepIndex < 3">4</span>
                        </div>
                        <span class="text-xs mt-1.5 font-medium transition-colors"
                            :class="stepIndex >= 3 ? 'text-brand font-semibold' : 'text-body'">Served</span>
                    </div>
                </div>
            </div>

            {{-- Items List --}}
            <div class="space-y-3">
                <h4 class="text-xs font-bold text-body uppercase tracking-wider mb-2">Order Items</h4>
                <template x-for="item in items" :key="item.id">
                    <div class="flex justify-between items-center py-2.5 px-3 rounded-lg bg-light border border-default/50 transition-colors">
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="font-medium text-dark" x-text="item.name"></p>
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                                    :class="{
                                        'bg-info/10 text-info': item.status === 'pending',
                                        'bg-brand/10 text-brand': item.status === 'preparing',
                                        'bg-success/10 text-success': item.status === 'ready',
                                        'bg-light text-body': item.status === 'served',
                                        'bg-danger/10 text-danger': item.status === 'cancelled'
                                    }"
                                    x-text="item.status"></span>
                            </div>
                            <p class="text-xs text-body mt-0.5">
                                Qty: <span x-text="item.quantity"></span> x $<span x-text="Number(item.unit_price).toFixed(2)"></span>
                            </p>
                        </div>
                        <span class="font-semibold text-dark" x-text="'$' + Number(item.subtotal).toFixed(2)"></span>
                    </div>
                </template>
            </div>

            {{-- Price Summary --}}
            <div class="mt-5 pt-4 border-t border-default space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-body">Subtotal</span>
                    <span class="text-dark">{{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-body">Tax (10%)</span>
                    <span class="text-dark">{{ number_format($order->tax_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-base font-bold pt-2 border-t border-default">
                    <span class="text-dark">Total</span>
                    <span class="text-brand">${{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="text-center pt-2">
            <a href="{{ route('public.order.form', $restaurantTable->qr_code) }}"
                class="btn-primary inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Place Another Order
            </a>
        </div>
    </div>

    <script>
        function orderConfirmationTracker() {
            return {
                orderId: {{ $order->id }},
                status: '{{ $order->status }}',
                items: @js($order->orderItems->map(fn($item) => [
                    'id' => $item->id,
                    'name' => $item->menu->name,
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'subtotal' => (float) $item->subtotal,
                    'status' => $item->status,
                ])->values()->all()),

                init() {
                    this.initEcho();
                },

                get stepIndex() {
                    const statusMap = {
                        pending: 0,
                        preparing: 1,
                        ready: 2,
                        served: 3,
                        completed: 3,
                        cancelled: -1
                    };
                    return statusMap[this.status] ?? 0;
                },

                get statusHeading() {
                    switch (this.status) {
                        case 'pending': return 'Order Received!';
                        case 'preparing': return 'Food in Preparation!';
                        case 'ready': return 'Your Order is Ready!';
                        case 'served':
                        case 'completed': return 'Order Served!';
                        case 'cancelled': return 'Order Cancelled';
                        default: return 'Order Status';
                    }
                },

                get statusMessage() {
                    switch (this.status) {
                        case 'pending': return 'Your order has been sent to the kitchen. Preparation will begin shortly.';
                        case 'preparing': return '🍳 The kitchen is now actively cooking your dishes.';
                        case 'ready': return '🎉 Your meal is ready! A server will bring it to your table right away.';
                        case 'served':
                        case 'completed': return '🍽️ Enjoy your meal! Thank you for dining with us.';
                        case 'cancelled': return 'This order was cancelled. Please contact staff if this was unexpected.';
                        default: return 'Tracking your order in real-time.';
                    }
                },

                initEcho() {
                    if (!window.Echo) {
                        console.warn('Echo not initialized');
                        return;
                    }

                    try {
                        window.Echo.channel(`orders.${this.orderId}`)
                            .listen('.order.status.updated', (e) => {
                                if (e.order_id === this.orderId) {
                                    this.status = e.new_status;
                                    if (['served', 'completed', 'cancelled'].includes(e.new_status)) {
                                        this.items.forEach(i => {
                                            if (i.status !== 'cancelled') {
                                                i.status = e.new_status === 'cancelled' ? 'cancelled' : 'served';
                                            }
                                        });
                                    }
                                    this.playNotification();
                                }
                            })
                            .listen('.order.item.status.updated', (e) => {
                                if (e.order_id === this.orderId) {
                                    const item = this.items.find(i => i.id === e.order_item_id);
                                    if (item) {
                                        item.status = e.new_status;
                                    }
                                }
                            });
                    } catch (err) {
                        console.error('Failed to subscribe to order channel:', err);
                    }
                },

                playNotification() {
                    try {
                        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdH+JkI2HfnR4goeOj4uFgH5/g4aLjIqFf31+gYKGioqJhYB+fYCEiIqKiYSAfX2Ag4eKiYiDf31+gYOFiIeFgH1+gIOGiIeFgH1+gIOGiIeFgH1+gIOGiIeFgH0=');
                        audio.volume = 0.4;
                        audio.play().catch(() => {});
                    } catch (e) {}
                }
            }
        }
    </script>
</x-layouts.public>
