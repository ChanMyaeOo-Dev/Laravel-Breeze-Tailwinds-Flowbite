<x-layouts.kitchen title="Kitchen Display" :token="$token">
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

        {{-- Orders Kanban Board --}}
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

            <div x-show="!loading && activeOrders.length > 0" class="grid grid-cols-3 gap-6 h-full">
                {{-- Pending Column --}}
                <div class="flex flex-col min-h-0">
                    <div class="flex items-center gap-2 mb-3 pb-3 border-b-2 border-info/30">
                        <span class="w-3 h-3 rounded-full bg-info"></span>
                        <h3 class="font-semibold text-dark">Pending</h3>
                        <span class="ml-auto text-sm font-medium text-body bg-info/10 px-2.5 py-0.5 rounded-full" x-text="ordersByStatus('pending').length"></span>
                    </div>
                    <div class="flex-1 overflow-y-auto space-y-3 pr-1">
                        <template x-for="order in sortedOrders.filter(o => o.status === 'pending')" :key="order.id">
                            <div class="bg-white rounded-lg border-l-4 border-info shadow-sm overflow-hidden transition-all hover:shadow-md">
                                <div class="px-4 py-3 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="font-bold text-dark" x-text="order.order_number"></span>
                                        <span x-show="order.table" class="text-xs font-medium text-body bg-light px-2 py-0.5 rounded" x-text="'T/' + (order.table?.table_number || '')"></span>
                                    </div>
                                    <span class="text-xs text-body" x-text="timeAgo(order.created_at)"></span>
                                </div>
                                <div class="px-4 pb-3 space-y-1.5">
                                    <template x-for="item in order.items" :key="item.id">
                                        <div class="flex items-center justify-between text-sm">
                                            <div class="flex items-center gap-2">
                                                <span class="text-body font-medium" x-text="item.quantity + 'x'"></span>
                                                <span class="text-dark" x-text="item.menu?.name"></span>
                                            </div>
                                            <span class="text-xs px-1.5 py-0.5 rounded font-medium"
                                                :class="{
                                                    'bg-info/10 text-info': item.status === 'pending',
                                                    'bg-brand/10 text-brand-soft': item.status === 'preparing',
                                                    'bg-success/10 text-success': item.status === 'ready',
                                                    'bg-light text-body': item.status === 'served',
                                                    'bg-danger/10 text-danger': item.status === 'cancelled'
                                                }"
                                                x-text="item.status"></span>
                                        </div>
                                    </template>
                                </div>
                                <div x-show="order.special_instructions" class="px-4 py-2 bg-info/5 border-t border-default">
                                    <p class="text-xs text-body"><span class="font-medium">Note:</span> <span x-text="order.special_instructions"></span></p>
                                </div>
                                <div class="px-4 py-3 border-t border-default flex gap-2">
                                    <button @click="updateOrderStatus(order, 'preparing')"
                                        class="flex-1 px-3 py-2 text-sm font-medium rounded-lg bg-brand text-white hover:bg-brand-soft transition-colors">
                                        Start Preparing
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Preparing Column --}}
                <div class="flex flex-col min-h-0">
                    <div class="flex items-center gap-2 mb-3 pb-3 border-b-2 border-brand/30">
                        <span class="w-3 h-3 rounded-full bg-brand"></span>
                        <h3 class="font-semibold text-dark">Preparing</h3>
                        <span class="ml-auto text-sm font-medium text-body bg-brand/10 px-2.5 py-0.5 rounded-full" x-text="ordersByStatus('preparing').length"></span>
                    </div>
                    <div class="flex-1 overflow-y-auto space-y-3 pr-1">
                        <template x-for="order in sortedOrders.filter(o => o.status === 'preparing')" :key="order.id">
                            <div class="bg-white rounded-lg border-l-4 border-brand shadow-sm overflow-hidden transition-all hover:shadow-md">
                                <div class="px-4 py-3 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="font-bold text-dark" x-text="order.order_number"></span>
                                        <span x-show="order.table" class="text-xs font-medium text-body bg-light px-2 py-0.5 rounded" x-text="'T/' + (order.table?.table_number || '')"></span>
                                    </div>
                                    <span class="text-xs text-body" x-text="timeAgo(order.created_at)"></span>
                                </div>
                                <div class="px-4 pb-3 space-y-1.5">
                                    <template x-for="item in order.items" :key="item.id">
                                        <div class="flex items-center justify-between text-sm">
                                            <div class="flex items-center gap-2">
                                                <span class="text-body font-medium" x-text="item.quantity + 'x'"></span>
                                                <span class="text-dark" x-text="item.menu?.name"></span>
                                            </div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-xs px-1.5 py-0.5 rounded font-medium"
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
                                                        class="text-xs px-2 py-0.5 rounded bg-brand text-white hover:bg-brand-soft transition-colors"
                                                        x-text="nextItemStatusLabel(item.status)">
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <div x-show="order.special_instructions" class="px-4 py-2 bg-info/5 border-t border-default">
                                    <p class="text-xs text-body"><span class="font-medium">Note:</span> <span x-text="order.special_instructions"></span></p>
                                </div>
                                <div class="px-4 py-3 border-t border-default flex gap-2">
                                    <button @click="updateOrderStatus(order, 'ready')"
                                        class="flex-1 px-3 py-2 text-sm font-medium rounded-lg bg-success text-white hover:opacity-90 transition-colors">
                                        Mark Ready
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Ready Column --}}
                <div class="flex flex-col min-h-0">
                    <div class="flex items-center gap-2 mb-3 pb-3 border-b-2 border-success/30">
                        <span class="w-3 h-3 rounded-full bg-success"></span>
                        <h3 class="font-semibold text-dark">Ready</h3>
                        <span class="ml-auto text-sm font-medium text-body bg-success/10 px-2.5 py-0.5 rounded-full" x-text="ordersByStatus('ready').length"></span>
                    </div>
                    <div class="flex-1 overflow-y-auto space-y-3 pr-1">
                        <template x-for="order in sortedOrders.filter(o => o.status === 'ready')" :key="order.id">
                            <div class="bg-white rounded-lg border-l-4 border-success shadow-sm overflow-hidden transition-all hover:shadow-md">
                                <div class="px-4 py-3 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="font-bold text-dark" x-text="order.order_number"></span>
                                        <span x-show="order.table" class="text-xs font-medium text-body bg-light px-2 py-0.5 rounded" x-text="'T/' + (order.table?.table_number || '')"></span>
                                    </div>
                                    <span class="text-xs text-body" x-text="timeAgo(order.created_at)"></span>
                                </div>
                                <div class="px-4 pb-3 space-y-1.5">
                                    <template x-for="item in order.items" :key="item.id">
                                        <div class="flex items-center justify-between text-sm">
                                            <div class="flex items-center gap-2">
                                                <span class="text-body font-medium" x-text="item.quantity + 'x'"></span>
                                                <span class="text-dark" x-text="item.menu?.name"></span>
                                            </div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-xs px-1.5 py-0.5 rounded font-medium"
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
                                                        class="text-xs px-2 py-0.5 rounded bg-brand text-white hover:bg-brand-soft transition-colors"
                                                        x-text="nextItemStatusLabel(item.status)">
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <div x-show="order.special_instructions" class="px-4 py-2 bg-info/5 border-t border-default">
                                    <p class="text-xs text-body"><span class="font-medium">Note:</span> <span x-text="order.special_instructions"></span></p>
                                </div>
                                <div class="px-4 py-3 border-t border-default flex gap-2">
                                    <button @click="updateOrderStatus(order, 'served')"
                                        class="flex-1 px-3 py-2 text-sm font-medium rounded-lg bg-dark text-white hover:opacity-90 transition-colors">
                                        Mark Served
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
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
                        if (!window.Echo) {
                            console.warn('Echo is not available');
                            this.startPolling();
                            return;
                        }

                        const pusherConn = window.Echo.connector?.pusher?.connection;
                        if (pusherConn) {
                            if (pusherConn.state === 'connected') {
                                this.echoConnected = true;
                                this.pollingMode = false;
                                this.stopPolling();
                            }
                            pusherConn.bind('connected', () => {
                                this.echoConnected = true;
                                this.pollingMode = false;
                                this.stopPolling();
                            });
                            pusherConn.bind('disconnected', () => {
                                this.echoConnected = false;
                                this.startPolling();
                            });
                            pusherConn.bind('unavailable', () => {
                                this.echoConnected = false;
                                this.startPolling();
                            });
                            pusherConn.bind('failed', () => {
                                this.echoConnected = false;
                                this.startPolling();
                            });
                        } else {
                            this.echoConnected = true;
                            this.pollingMode = false;
                            this.stopPolling();
                        }

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
                            })
                            .error((err) => {
                                console.error('Kitchen channel error:', err);
                                this.echoConnected = false;
                                this.startPolling();
                            });
                    } catch (err) {
                        console.error('Echo init failed:', err);
                        this.echoConnected = false;
                        this.startPolling();
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
                    let audio = new Audio({{ Js::from(asset('sounds/order_noti.wav')) }});
                    audio.volume = 0.99;
                    audio.muted = true; // Add this line
                    audio.play().catch(() => {});
                    } catch (e) {}
                },
            }
        }
    </script>
</x-layouts.kitchen>
