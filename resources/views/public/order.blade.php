<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Order at Table {{ $restaurantTable->table_number }} - {{ $restaurantTable->restaurant->name }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-light min-h-screen font-sans">
        <div class="min-h-screen flex flex-col">
            <header class="bg-white shadow-sm border-b border-default">
                <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-dark">{{ $restaurantTable->restaurant->name }}</h1>
                        <p class="text-sm text-body">Table {{ $restaurantTable->table_number }}</p>
                    </div>
                    <div class="text-sm text-body">Scan & Order</div>
                </div>
            </header>

            <main class="flex-1 max-w-4xl mx-auto w-full px-4 py-6">
                @if (session('success'))
                    <div class="mb-6 p-4 text-sm rounded-lg bg-success/10 text-success border border-success/20" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 text-sm rounded-lg bg-danger/10 text-danger border border-danger/20">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('public.order.store', $restaurantTable->qr_code) }}" method="POST" x-data="orderForm()">
                    @csrf

                    <div class="flex flex-col lg:flex-row gap-6">
                        <div class="flex-1 min-w-0">
                            <div class="relative mb-4">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-body" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input type="text" x-model.debounce.300ms="search"
                                    class="block w-full p-3 pl-10 text-sm text-dark bg-white border border-default rounded-lg focus:ring-brand focus:border-brand"
                                    placeholder="Search menu items...">
                                <button x-show="search" @click="search = ''" type="button"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-body hover:text-dark">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="flex gap-2 mb-4 overflow-x-auto pb-2">
                                <button type="button" @click="activeCategory = null"
                                    class="flex-shrink-0 px-4 py-2 text-sm font-medium rounded-full transition-colors"
                                    :class="activeCategory === null ? 'bg-brand text-white' : 'bg-light text-dark hover:bg-gray-200'">
                                    All
                                </button>
                                @foreach ($menuCategories as $category)
                                    <button type="button" @click="activeCategory = {{ $category->id }}"
                                        class="flex-shrink-0 px-4 py-2 text-sm font-medium rounded-full transition-colors"
                                        :class="activeCategory === {{ $category->id }} ? 'bg-brand text-white' : 'bg-light text-dark hover:bg-gray-200'">
                                        {{ $category->name }}
                                    </button>
                                @endforeach
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                                <template x-for="menu in filteredMenus" :key="menu.id">
                                    <div @click="addToCart(menu)"
                                        class="relative cursor-pointer rounded-xl border-2 transition-all duration-200 overflow-hidden"
                                        :class="inCart(menu.id) ? 'border-brand bg-brand/5 shadow-md' : 'border-default bg-white hover:border-brand-soft hover:shadow-md'">

                                        <div x-show="inCart(menu.id)"
                                            class="absolute top-2 right-2 z-10 bg-brand text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center"
                                            x-text="cartQuantity(menu.id)">
                                        </div>

                                        <div class="h-36 bg-light flex items-center justify-center overflow-hidden">
                                            <template x-if="menu.image">
                                                <img :src="menu.image_url" :alt="menu.name" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!menu.image">
                                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0A1.5 1.5 0 003 15.546M9 6v2a3 3 0 006 0V6M9 6H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V8a2 2 0 00-2-2h-3"/>
                                                </svg>
                                            </template>
                                        </div>

                                        <div class="p-3">
                                            <h3 class="font-semibold text-dark text-sm truncate" x-text="menu.name"></h3>
                                            <p class="text-brand font-bold text-base mt-1" x-text="formatCurrency(menu.price)"></p>
                                        </div>

                                        <div x-show="inCart(menu.id)" @click.stop class="px-3 pb-3">
                                            <div class="flex items-center justify-between bg-light rounded-lg p-2">
                                                <button type="button" @click="updateQty(menu.id, -1)"
                                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-white text-dark hover:bg-gray-200 shadow-sm transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                    </svg>
                                                </button>
                                                <span class="font-semibold text-dark w-8 text-center" x-text="cartQuantity(menu.id)"></span>
                                                <button type="button" @click="updateQty(menu.id, 1)"
                                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-brand text-white hover:bg-brand-soft shadow-sm transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div x-show="filteredMenus.length === 0" class="text-center py-16">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <p class="text-body text-lg">No menu items found</p>
                                <p class="text-body text-sm mt-1">Try adjusting your search or category filter</p>
                            </div>
                        </div>

                        <div class="w-full lg:w-96 flex-shrink-0">
                            <div class="lg:sticky lg:top-6">
                                <div class="bg-white border border-default rounded-xl shadow-sm overflow-hidden">
                                    <div class="bg-brand px-5 py-4">
                                        <h3 class="text-lg font-semibold text-white">Your Order</h3>
                                        <p class="text-white/70 text-sm mt-0.5" x-text="cart.length + ' item(s) selected'"></p>
                                    </div>

                                    <div class="max-h-80 overflow-y-auto divide-y divide-light">
                                        <template x-if="cart.length === 0">
                                            <div class="p-8 text-center">
                                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                                                </svg>
                                                <p class="text-body text-sm">Your order is empty</p>
                                                <p class="text-body text-xs mt-1">Click on menu items to add them</p>
                                            </div>
                                        </template>
                                        <template x-for="(item, index) in cart" :key="item.menu_id">
                                            <div class="p-4 hover:bg-light transition-colors">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div class="flex-1 min-w-0">
                                                        <h4 class="font-medium text-dark text-sm truncate" x-text="item.name"></h4>
                                                        <p class="text-body text-xs mt-0.5" x-text="formatCurrency(item.price) + ' each'"></p>
                                                    </div>
                                                    <button type="button" @click="removeFromCart(index)"
                                                        class="text-body hover:text-danger transition-colors flex-shrink-0">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                    </button>
                                                </div>

                                                <div class="flex items-center justify-between mt-2">
                                                    <div class="flex items-center gap-1">
                                                        <button type="button" @click="updateQty(item.menu_id, -1)"
                                                            class="w-7 h-7 flex items-center justify-center rounded-md bg-light text-dark hover:bg-gray-200 transition-colors text-xs">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                            </svg>
                                                        </button>
                                                        <span class="w-8 text-center text-sm font-medium text-dark" x-text="item.quantity"></span>
                                                        <button type="button" @click="updateQty(item.menu_id, 1)"
                                                            class="w-7 h-7 flex items-center justify-center rounded-md bg-brand text-white hover:bg-brand-soft transition-colors text-xs">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <span class="font-semibold text-dark text-sm" x-text="formatCurrency(item.price * item.quantity)"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    <div class="px-5 py-3 border-t border-light">
                                        <label for="special_instructions" class="block text-xs font-medium text-body mb-1">Special Instructions</label>
                                        <textarea name="special_instructions" id="special_instructions" rows="2"
                                            class="block w-full text-sm text-dark bg-light border border-default rounded-lg focus:ring-brand focus:border-brand"
                                            placeholder="Any special requests?"></textarea>
                                    </div>

                                    <div x-show="cart.length > 0" class="px-5 py-4 bg-light border-t border-light space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-body">Subtotal</span>
                                            <span class="font-medium text-dark" x-text="formatCurrency(subtotal)"></span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-body">Tax (10%)</span>
                                            <span class="font-medium text-dark" x-text="formatCurrency(tax)"></span>
                                        </div>
                                        <div class="flex justify-between text-base font-bold pt-2 border-t border-default">
                                            <span class="text-dark">Total</span>
                                            <span class="text-brand" x-text="formatCurrency(total)"></span>
                                        </div>
                                    </div>

                                    <div class="p-5">
                                        <button type="submit" class="btn-primary w-full justify-center text-base py-3"
                                            :disabled="cart.length === 0 || loading">
                                            <span x-show="!loading" class="flex items-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Place Order
                                            </span>
                                            <span x-show="loading" class="flex items-center justify-center gap-2">
                                                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Placing Order...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <template x-for="(item, index) in cart" :key="'input-' + item.menu_id">
                        <div>
                            <input type="hidden" :name="'items[' + index + '][menu_id]'" :value="item.menu_id">
                            <input type="hidden" :name="'items[' + index + '][quantity]'" :value="item.quantity">
                            <input type="hidden" :name="'items[' + index + '][notes]'" :value="item.notes || ''">
                        </div>
                    </template>
                </form>
            </main>

            <footer class="bg-white border-t border-default py-4">
                <div class="max-w-4xl mx-auto px-4 text-center text-sm text-body">Powered by SmartServe</div>
            </footer>
        </div>

        <script>
            function orderForm() {
                return {
                    search: '',
                    activeCategory: null,
                    cart: [],
                    loading: false,
                    menus: @js($menus->map(fn ($m) => [
                        'id' => $m->id,
                        'name' => $m->name,
                        'price' => (float) $m->price,
                        'image' => $m->image,
                        'image_url' => $m->image ? Storage::disk(config('image.disk', 's3'))->url($m->image) : null,
                        'category_id' => $m->menu_category_id,
                    ])),
                    get filteredMenus() {
                        return this.menus.filter(m => {
                            const matchSearch = !this.search || m.name.toLowerCase().includes(this.search.toLowerCase());
                            const matchCategory = !this.activeCategory || m.category_id == this.activeCategory;
                            return matchSearch && matchCategory;
                        });
                    },
                    inCart(menuId) { return this.cart.some(item => item.menu_id === menuId); },
                    cartQuantity(menuId) { const item = this.cart.find(item => item.menu_id === menuId); return item ? item.quantity : 0; },
                    addToCart(menu) {
                        const existing = this.cart.find(item => item.menu_id === menu.id);
                        if (existing) { existing.quantity++; } else {
                            this.cart.push({ menu_id: menu.id, name: menu.name, price: menu.price, quantity: 1, notes: '' });
                        }
                    },
                    removeFromCart(index) { this.cart.splice(index, 1); },
                    updateQty(menuId, delta) {
                        const item = this.cart.find(item => item.menu_id === menuId);
                        if (item) { item.quantity = Math.max(1, item.quantity + delta); }
                    },
                    get subtotal() { return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0); },
                    get tax() { return this.subtotal * 0.1; },
                    get total() { return this.subtotal + this.tax; },
                    formatCurrency(value) {
                        return new Intl.NumberFormat('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
                    },
                }
            }
        </script>
    </body>
</html>
