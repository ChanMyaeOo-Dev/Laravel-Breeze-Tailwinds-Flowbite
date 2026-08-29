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

        {{-- Feedback Card --}}
        <div x-show="status === 'served' || status === 'completed'" x-transition.opacity x-cloak
            class="bg-white border border-default rounded-xl shadow-sm overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-default bg-gradient-to-r from-brand/[0.04] to-transparent">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-brand/10 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.921-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-dark">How was your experience?</h3>
                        <p class="text-xs text-body mt-0.5">Your feedback helps us improve — takes 30 seconds.</p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-5">
                @if (session('feedback_success'))
                    {{-- Success State --}}
                    <div class="text-center py-4">
                        <div class="w-14 h-14 rounded-full bg-success/10 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h4 class="text-base font-semibold text-dark">Thank you for your feedback!</h4>
                        <p class="text-sm text-body mt-1">We appreciate you taking the time to share your experience.</p>
                    </div>
                @elseif(isset($existingFeedback) && $existingFeedback)
                    {{-- Already Submitted State --}}
                    <div class="text-center py-2">
                        <div class="w-14 h-14 rounded-full bg-success/10 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h4 class="text-base font-semibold text-dark">Feedback already submitted</h4>
                        <p class="text-sm text-body mt-1">Thank you — we received your review for this order.</p>
                        <div class="mt-4 p-4 bg-light rounded-lg border border-default text-left">
                            <div class="flex items-center gap-1 mb-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $existingFeedback->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                                <span class="text-xs text-body ml-1">{{ $existingFeedback->rating }}/5</span>
                            </div>
                            <p class="text-sm text-dark leading-relaxed">{{ $existingFeedback->comment }}</p>
                        </div>
                    </div>
                @else
                    {{-- Feedback Form --}}
                    <form method="POST" action="{{ route('public.feedback.store', $restaurantTable->qr_code) }}" x-data="feedbackForm()" @submit="submitting = true">
                        @csrf

                        {{-- Star Rating --}}
                        <div>
                            <label class="block text-sm font-medium text-dark mb-2">Rating <span class="text-danger">*</span></label>
                            <div class="flex items-center gap-1.5" role="radiogroup" aria-label="Star rating">
                                <template x-for="star in 5" :key="star">
                                    <button type="button"
                                        @click="rating = star"
                                        @mouseenter="hover = star"
                                        @mouseleave="hover = 0"
                                        @keydown.arrow-right.prevent="rating = Math.min(5, rating + 1)"
                                        @keydown.arrow-left.prevent="rating = Math.max(1, rating - 1)"
                                        :aria-label="'Rate ' + star + ' stars'"
                                        :class="{
                                            'scale-110': hover === star,
                                            'text-yellow-400': (hover ? star <= hover : star <= rating),
                                            'text-gray-300': !(hover ? star <= hover : star <= rating)
                                        }"
                                        class="transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-brand/30 rounded p-0.5">
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </button>
                                </template>
                                <span class="ml-2 text-sm font-medium min-w-[60px]" :class="rating ? 'text-dark' : 'text-body'">
                                    <span x-text="rating ? rating + '/5' : 'Tap to rate'"></span>
                                </span>
                            </div>
                            <input type="hidden" name="rating" :value="rating">
                            @error('rating')
                                <p class="text-xs text-danger mt-1.5">{{ $message }}</p>
                            @enderror
                            <p x-show="showRatingError" x-cloak class="text-xs text-danger mt-1.5">Please select a star rating.</p>
                        </div>

                        {{-- Comment --}}
                        <div class="mt-5">
                            <label for="feedback-comment" class="block text-sm font-medium text-dark mb-2">Your experience <span class="text-danger">*</span></label>
                            <textarea id="feedback-comment" name="comment" rows="4" maxlength="1000" required
                                x-model="comment"
                                placeholder="Tell us what you loved or how we can do better..."
                                class="w-full rounded-lg border border-default bg-white px-3.5 py-2.5 text-sm text-dark placeholder:text-body/60 focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none transition-colors resize-none">{{ old('comment') }}</textarea>
                            <div class="flex justify-between mt-1.5">
                                <span>
                                    @error('comment')
                                        <span class="text-xs text-danger">{{ $message }}</span>
                                    @enderror
                                </span>
                                <span class="text-xs ml-auto" :class="comment.length > 900 ? 'text-danger font-medium' : 'text-body'" x-text="comment.length + '/1000'"></span>
                            </div>
                        </div>

                        @error('feedback')
                            <p class="text-xs text-danger mt-3">{{ $message }}</p>
                        @enderror

                        {{-- Submit --}}
                        <button type="submit"
                            @click="if(!rating){ showRatingError = true; $event.preventDefault(); }"
                            :disabled="submitting"
                            class="mt-5 w-full inline-flex items-center justify-center gap-2 bg-brand text-white font-medium rounded-lg px-5 py-3 text-sm hover:bg-brand-soft focus:outline-none focus:ring-2 focus:ring-brand/30 disabled:opacity-60 disabled:cursor-not-allowed transition-colors">
                            <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span x-text="submitting ? 'Submitting...' : 'Submit Feedback'"></span>
                        </button>
                        <p class="text-xs text-body text-center mt-2">One feedback per order — you can edit it later via staff.</p>
                    </form>

                    <script>
                        function feedbackForm() {
                            return {
                                rating: @js((int) old('rating', 0)),
                                hover: 0,
                                comment: @js(old('comment', '')),
                                submitting: false,
                                showRatingError: false,
                            }
                        }
                    </script>
                @endif
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
