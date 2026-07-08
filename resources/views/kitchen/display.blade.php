<x-layouts.kitchen title="Kitchen Display">
    <div x-data="kitchenDisplay()" x-init="init()" class="min-h-screen flex flex-col">
        {{-- Header --}}
        <header class="bg-white border-b border-default px-6 py-3 flex items-center justify-between sticky top-0 z-40">
            <div class="flex items-center gap-3">
                <h1 class="text-xl font-bold text-dark">{{ $restaurant->name }}</h1>
                <span class="text-sm text-body">Kitchen Display</span>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"
                            :class="echoConnected ? 'bg-success' : 'bg-danger'"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3"
                            :class="echoConnected ? 'bg-success' : 'bg-danger'"></span>
                    </span>
                    <span class="text-sm text-body" x-text="echoConnected ? 'Live' : (pollingMode ? 'Polling' : 'Disconnected')"></span>
                </div>
                <span class="text-sm text-body" x-text="currentTime"></span>
                <button @click="refreshOrders()" class="text-sm text-brand hover:text-brand-soft font-medium">
                    Refresh
                </button>
                <form method="POST" action="{{ route('kitchen.logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-body hover:text-danger font-medium">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        {{-- Stats Bar --}}
        <div class="bg-white border-b border-default px-6 py-2 flex items-center gap-6">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-info"></span>
                <span class="text-sm font-medium text-dark">Pending</span>
                <span class="text-sm text-body" x-text="ordersByStatus('pending').length"></span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-brand"></span>
                <span class="text-sm font-medium text-dark">Preparing</span>
                <span class="text-sm text-body" x-text="ordersByStatus('preparing').length"></span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-success"></span>
                <span class="text-sm font-medium text-dark">Ready</span>
                <span class="text-sm text-body" x-text="ordersByStatus('ready').length"></span>
            </div>
            <div class="flex items-center gap-2 ml-auto">
                <span class="text-sm font-medium text-dark">Total Active:</span>
                <span class="text-sm text-body" x-text="activeOrders.length"></span>
            </div>
        </div>

        {{-- Orders Grid --}}
        <main class="flex-1 p-6 overflow-auto">
            <div x-show="loading" class="flex items-center justify-center py-20">
                <svg class="animate-spin w-8 h-8 text-brand" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <div x-show="!loading && activeOrders.length === 0" class="flex flex-col items-center justify-center py-20">
                <svg class="w-16 h-16 text-body mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-lg text-dark">No active orders</p>
                <p class="text-sm text-body mt-1">New orders will appear here automatically</p>
            </div>

            <div x-show="!loading && activeOrders.length > 0"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <template x-for="order in sortedOrders" :key="order.id">
                    <div class="bg-white rounded-xl border border-default shadow-sm overflow-hidden transition-all duration-300"
                        :class="{
                            'ring-2 ring-info/50': order.status === 'pending',
                            'ring-2 ring-brand/50': order.status === 'preparing',
                            'ring-2 ring-success/50': order.status === 'ready'
                        }">
                        {{-- Card Header --}}
                        <div class="px-4 py-3 border-b border-default flex items-center justify-between"
                            :class="{
                                'bg-info/5': order.status === 'pending',
                                'bg-brand/5': order.status === 'preparing',
                                'bg-success/5': order.status === 'ready'
                            }">
                            <div>
                                <span class="font-bold text-dark" x-text="order.order_number"></span>
                                <span class="text-sm text-body ml-2" x-show="order.table" x-text="'Table ' + (order.table?.table_number || '')"></span>
                            </div>
                            <span class="text-xs text-body" x-text="timeAgo(order.created_at)"></span>
                        </div>

                        {{-- Items --}}
                        <div class="px-4 py-3 max-h-48 overflow-y-auto">
                            <template x-for="item in order.items" :key="item.id">
                                <div class="flex items-start justify-between py-1.5 border-b border-light last:border-0">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium text-dark text-sm" x-text="item.quantity + 'x'"></span>
                                            <span class="text-sm text-dark" x-text="item.menu?.name"></span>
                                        </div>
                                        <p x-show="item.notes" class="text-xs text-body mt-0.5" x-text="item.notes"></p>
                                    </div>
                                    <div class="flex items-center gap-1.5 ml-2">
                                        <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                                            :class="{
                                                'bg-info/10 text-info': item.status === 'pending',
                                                'bg-brand/10 text-brand-soft': item.status === 'preparing',
                                                'bg-success/10 text-success': item.status === 'ready',
                                                'bg-light text-body': item.status === 'served',
                                                'bg-danger/10 text-danger': item.status === 'cancelled'
                                            }"
                                            x-text="item.status"></span>
                                        <template x-if="item.status !== 'ready' && item.status !== 'served' && item.status !== 'cancelled'">
                                            <button @click="updateItemStatus(order, item)"
                                                class="text-xs px-2 py-0.5 rounded-full bg-brand text-white hover:bg-brand-soft transition-colors"
                                                x-text="nextItemStatusLabel(item.status)">
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Special Instructions --}}
                        <div x-show="order.special_instructions" class="px-4 py-2 bg-info/5 border-t border-default">
                            <p class="text-xs text-body">
                                <span class="font-medium">Note:</span>
                                <span x-text="order.special_instructions"></span>
                            </p>
                        </div>

                        {{-- Card Footer: Status Actions --}}
                        <div class="px-4 py-3 border-t border-default flex gap-2">
                            <template x-if="order.status === 'pending'">
                                <button @click="updateOrderStatus(order, 'preparing')"
                                    class="flex-1 px-3 py-2 text-sm font-medium rounded-lg bg-brand text-white hover:bg-brand-soft transition-colors">
                                    Start Preparing
                                </button>
                            </template>
                            <template x-if="order.status === 'preparing'">
                                <button @click="updateOrderStatus(order, 'ready')"
                                    class="flex-1 px-3 py-2 text-sm font-medium rounded-lg bg-success text-white hover:opacity-90 transition-colors">
                                    Mark Ready
                                </button>
                            </template>
                            <template x-if="order.status === 'ready'">
                                <button @click="updateOrderStatus(order, 'served')"
                                    class="flex-1 px-3 py-2 text-sm font-medium rounded-lg bg-dark text-white hover:opacity-90 transition-colors">
                                    Mark Served
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </main>
    </div>

    <script>
        function kitchenDisplay() {
            return {
                orders: [],
                loading: true,
                echoConnected: false,
                pollingMode: false,
                currentTime: '',
                now: Date.now(),
                token: '{{ $token }}',
                restaurantId: {{ $restaurant->id }},
                echoInstance: null,
                pollingInterval: null,
                reconnectInterval: null,
                clockInterval: null,

                async init() {
                    this.updateClock();
                    this.clockInterval = setInterval(() => this.updateClock(), 1000);
                    setInterval(() => { this.now = Date.now(); }, 30000);

                    await this.fetchOrders();
                    this.loading = false;

                    this.initEcho();
                },

                updateClock() {
                    this.currentTime = new Date().toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    });
                },

                initEcho() {
                    try {
                        this.echoInstance = window.Echo.private(`restaurant.${this.restaurantId}.kitchen`)
                            .listen('.order.received', (e) => {
                                this.addOrder(e.order);
                                this.playNotificationSound();
                            })
                            .listen('.order.status.updated', (e) => {
                                this.updateOrderStatusFromEvent(e);
                            })
                            .listen('.order.item.status.updated', (e) => {
                                this.updateItemStatusFromEvent(e);
                            });

                        this.echoConnected = true;
                        this.pollingMode = false;
                        this.stopPolling();
                        this.stopReconnecting();

                        this.echoInstance.error(() => {
                            this.echoConnected = false;
                            this.startPolling();
                            this.startReconnecting();
                        });
                    } catch (err) {
                        console.error('Echo init failed:', err);
                        this.echoConnected = false;
                        this.startPolling();
                        this.startReconnecting();
                    }
                },

                startReconnecting() {
                    if (this.reconnectInterval) return;
                    this.reconnectInterval = setInterval(() => {
                        this.initEcho();
                    }, 10000);
                },

                stopReconnecting() {
                    if (this.reconnectInterval) {
                        clearInterval(this.reconnectInterval);
                        this.reconnectInterval = null;
                    }
                },

                startPolling() {
                    if (this.pollingInterval) return;
                    this.pollingMode = true;
                    this.pollingInterval = setInterval(() => this.fetchOrders(), 30000);
                },

                stopPolling() {
                    if (this.pollingInterval) {
                        clearInterval(this.pollingInterval);
                        this.pollingInterval = null;
                    }
                },

                get activeOrders() {
                    return this.orders.filter(o => ['pending', 'preparing', 'ready'].includes(o.status));
                },

                get sortedOrders() {
                    const statusOrder = { pending: 0, preparing: 1, ready: 2 };
                    return [...this.activeOrders].sort((a, b) => {
                        const statusDiff = (statusOrder[a.status] ?? 3) - (statusOrder[b.status] ?? 3);
                        if (statusDiff !== 0) return statusDiff;
                        return new Date(a.created_at) - new Date(b.created_at);
                    });
                },

                ordersByStatus(status) {
                    return this.activeOrders.filter(o => o.status === status);
                },

                async fetchOrders() {
                    try {
                        const response = await fetch('/api/kitchen/orders', {
                            headers: {
                                'Authorization': `Bearer ${this.token}`,
                                'Accept': 'application/json',
                            }
                        });
                        if (response.ok) {
                            const data = await response.json();
                            this.orders = data.orders || [];
                        }
                    } catch (err) {
                        console.error('Fetch orders failed:', err);
                    }
                },

                async refreshOrders() {
                    await this.fetchOrders();
                },

                addOrder(order) {
                    const exists = this.orders.find(o => o.id === order.id);
                    if (!exists) {
                        this.orders.unshift(order);
                    }
                },

                updateOrderStatusFromEvent(e) {
                    const order = this.orders.find(o => o.id === e.order_id);
                    if (order) {
                        order.status = e.new_status;
                        if (['served', 'completed', 'cancelled'].includes(e.new_status)) {
                            order.items.forEach(item => {
                                if (item.status !== 'cancelled') {
                                    item.status = e.new_status === 'cancelled' ? 'cancelled' : 'served';
                                }
                            });
                        }
                    }
                },

                updateItemStatusFromEvent(e) {
                    const order = this.orders.find(o => o.id === e.order_id);
                    if (order) {
                        const item = order.items.find(i => i.id === e.order_item_id);
                        if (item) {
                            item.status = e.new_status;
                        }
                    }
                },

                async updateOrderStatus(order, newStatus) {
                    try {
                        const response = await fetch(`/api/kitchen/orders/${order.id}/status`, {
                            method: 'PATCH',
                            headers: {
                                'Authorization': `Bearer ${this.token}`,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ status: newStatus }),
                        });

                        if (response.ok) {
                            const data = await response.json();
                            order.status = data.order.status;
                            order.items = data.order.items;
                        }
                    } catch (err) {
                        console.error('Update order status failed:', err);
                    }
                },

                async updateItemStatus(order, item) {
                    const nextStatus = this.nextItemStatus(item.status);
                    if (!nextStatus) return;

                    try {
                        const response = await fetch(`/api/kitchen/orders/${order.id}/items/${item.id}/status`, {
                            method: 'PATCH',
                            headers: {
                                'Authorization': `Bearer ${this.token}`,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ status: nextStatus }),
                        });

                        if (response.ok) {
                            const data = await response.json();
                            item.status = data.order_item.status;
                        }
                    } catch (err) {
                        console.error('Update item status failed:', err);
                    }
                },

                nextItemStatus(current) {
                    const flow = { pending: 'preparing', preparing: 'ready', ready: 'served' };
                    return flow[current] || null;
                },

                nextItemStatusLabel(current) {
                    const labels = { pending: 'Start', preparing: 'Ready', ready: 'Serve' };
                    return labels[current] || '';
                },

                timeAgo(dateString) {
                    const diff = Math.floor((this.now - new Date(dateString).getTime()) / 1000);

                    if (diff < 0) return 'just now';
                    if (diff < 60) return diff + 's ago';
                    if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
                    if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
                    return Math.floor(diff / 86400) + 'd ago';
                },

                playNotificationSound() {
                    try {
                        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdH+JkI2HfnR4goeOj4uFgH5/g4aLjIqFf31+gYKGioqJhYB+fYCEiIqKiYSAfX2Ag4eKiYiDf31+gYOFiIeFgH1+gIOGiIeFgH1+gIOGiIeFgH1+gIOGiIeFgH0=');
                        audio.volume = 0.3;
                        audio.play().catch(() => {});
                    } catch (e) {}
                },
            }
        }
    </script>
</x-layouts.kitchen>
