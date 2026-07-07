@extends('layouts.admin')

@section('title', 'Point of Sale')

@section('content')
<div x-data="pos({
        searchUrl: '{{ route('admin.sales.search') }}',
        preselectCustomer: '{{ request('customer_id') }}'
     })"
     x-init="init()"
     @keydown.window.f2.prevent="$refs.search.focus()">

    @if ($errors->any())
        <div class="rounded border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700 mb-4">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-2xl font-bold">Point of Sale</h1>
            <p class="text-sm text-gray-600">Search a medicine (or press <kbd class="px-1 border rounded">F2</kbd>), add to cart, take payment.</p>
        </div>
        <a href="{{ route('admin.sales.index') }}" class="px-4 py-2 rounded border">All Sales</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- LEFT: search + cart --}}
        <div class="lg:col-span-2 space-y-4">
            {{-- Search box --}}
            <div class="bg-white shadow rounded p-4 relative">
                <label class="block text-sm font-medium mb-1">Find medicine</label>
                <input type="text" x-ref="search" x-model="term"
                       @input.debounce.250ms="search()"
                       @keydown.enter.prevent="results.length && addToCart(results[0])"
                       @keydown.escape="results = []"
                       placeholder="Type name, SKU or scan barcode…"
                       class="w-full border rounded px-3 py-2" autocomplete="off">

                <div x-show="loading" class="absolute right-6 top-9 text-xs text-gray-400">searching…</div>

                <ul x-show="results.length" x-cloak
                    class="absolute z-20 left-4 right-4 mt-1 bg-white border rounded shadow-lg max-h-72 overflow-y-auto">
                    <template x-for="p in results" :key="p.id">
                        <li @click="addToCart(p)"
                            class="px-3 py-2 hover:bg-emerald-50 cursor-pointer flex items-center justify-between">
                            <div>
                                <p class="font-medium text-sm" x-text="p.name"></p>
                                <p class="text-xs text-gray-500">
                                    <span x-text="p.sku"></span> ·
                                    <span x-text="'Stock: ' + p.stock"></span> ·
                                    <span x-text="'Exp: ' + (p.nearest_expiry || '—')"></span>
                                </p>
                            </div>
                            <span class="text-sm font-semibold">₹<span x-text="Number(p.price).toFixed(2)"></span></span>
                        </li>
                    </template>
                </ul>
                <p x-show="term.length > 1 && !loading && !results.length" x-cloak
                   class="text-xs text-gray-400 mt-2">No in-stock matches.</p>
            </div>

            {{-- Cart --}}
            <div class="bg-white shadow rounded">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-left">
                            <tr>
                                <th class="p-3">Medicine</th>
                                <th class="p-3 w-24">Price</th>
                                <th class="p-3 w-24">Qty</th>
                                <th class="p-3 w-24">Disc %</th>
                                <th class="p-3 w-16">GST %</th>
                                <th class="p-3 w-28 text-right">Total</th>
                                <th class="p-3 w-8"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, i) in cart" :key="item.id">
                                <tr class="border-t align-middle">
                                    <td class="p-3">
                                        <p class="font-medium" x-text="item.name"></p>
                                        <p class="text-xs text-gray-500">
                                            <span x-text="item.sku"></span> ·
                                            <span x-text="'in stock: ' + item.stock"></span>
                                        </p>
                                    </td>
                                    <td class="p-3">₹<span x-text="Number(item.price).toFixed(2)"></span></td>
                                    <td class="p-3">
                                        <input type="number" min="1" :max="item.stock" x-model.number="item.qty"
                                               @input="clampQty(item)"
                                               class="w-20 border rounded px-2 py-1">
                                    </td>
                                    <td class="p-3">
                                        <input type="number" min="0" max="100" step="0.01" x-model.number="item.discount"
                                               class="w-20 border rounded px-2 py-1">
                                    </td>
                                    <td class="p-3 text-gray-600" x-text="Number(item.gst).toFixed(0)"></td>
                                    <td class="p-3 text-right font-medium">₹<span x-text="lineTotal(item).toFixed(2)"></span></td>
                                    <td class="p-3">
                                        <button type="button" @click="removeItem(i)" class="text-rose-600 hover:underline">✕</button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="!cart.length">
                                <td colspan="7" class="p-6 text-center text-gray-400">Cart is empty. Search to add medicines.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- RIGHT: checkout --}}
        <div class="space-y-4">
            <form method="POST" action="{{ route('admin.sales.store') }}" @submit="prepare($event)"
                  class="bg-white shadow rounded p-5 space-y-4">
                @csrf
                <input type="hidden" name="items_json" x-ref="itemsJson">
                <input type="hidden" name="action" x-ref="action">

                <div>
                    <label class="block text-sm font-medium mb-1">Customer</label>
                    <select name="customer_id" x-model="customerId" class="w-full border rounded px-3 py-2">
                        <option value="">Walk-in customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">
                                {{ $customer->name }}{{ $customer->phone ? ' — '.$customer->phone : '' }}
                            </option>
                        @endforeach
                    </select>
                    <a href="{{ route('admin.customers.create') }}" target="_blank"
                       class="text-xs text-emerald-700 hover:underline">+ Add new customer</a>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Payment Method</label>
                    <select name="payment_method" x-model="paymentMethod" class="w-full border rounded px-3 py-2">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="upi">UPI</option>
                        <option value="credit">Credit (pay later)</option>
                    </select>
                </div>

                <div class="border-t pt-3 space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span>₹<span x-text="subtotal().toFixed(2)"></span></span></div>
                    <div class="flex justify-between"><span class="text-gray-500">GST</span><span>₹<span x-text="taxTotal().toFixed(2)"></span></span></div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Bill Discount ₹</span>
                        <input type="number" min="0" step="0.01" name="discount_amount" x-model.number="headerDiscount"
                               class="w-28 border rounded px-2 py-1 text-right">
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t pt-2">
                        <span>Grand Total</span><span>₹<span x-text="grandTotal().toFixed(2)"></span></span>
                    </div>
                </div>

                <div class="border-t pt-3 space-y-2 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Amount Paid ₹</span>
                        <input type="number" min="0" step="0.01" name="paid_amount" x-model.number="paidAmount"
                               @input="paidTouched = true"
                               class="w-28 border rounded px-2 py-1 text-right">
                    </div>
                    <div class="flex justify-between" x-show="change() > 0" x-cloak>
                        <span class="text-gray-500">Change to return</span><span class="text-emerald-600 font-semibold">₹<span x-text="change().toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between" x-show="due() > 0" x-cloak>
                        <span class="text-gray-500">Balance due</span><span class="text-rose-600 font-semibold">₹<span x-text="due().toFixed(2)"></span></span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Notes</label>
                    <input type="text" name="notes" x-model="notes" class="w-full border rounded px-3 py-2" placeholder="Optional">
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button type="submit" @click="actionType = 'save'" :disabled="!cart.length"
                            class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700 disabled:opacity-40">
                        Save Bill
                    </button>
                    <button type="submit" @click="actionType = 'save_print'" :disabled="!cart.length"
                            class="bg-slate-800 text-white px-4 py-2 rounded hover:bg-slate-900 disabled:opacity-40">
                        Save &amp; Print
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function pos(config) {
        return {
            searchUrl: config.searchUrl,
            term: '',
            results: [],
            loading: false,
            cart: [],
            customerId: config.preselectCustomer || '',
            paymentMethod: 'cash',
            headerDiscount: 0,
            paidAmount: 0,
            paidTouched: false,
            notes: '',
            actionType: 'save',

            init() {
                // Keep "amount paid" mirroring the grand total until the cashier
                // types their own figure (e.g. a part payment on a credit bill).
                this.$watch('cart', () => this.syncPaid(), { deep: true });
                this.$watch('headerDiscount', () => this.syncPaid());
            },

            syncPaid() {
                if (!this.paidTouched) {
                    this.paidAmount = Number(this.grandTotal().toFixed(2));
                }
            },

            async search() {
                const q = this.term.trim();
                if (q.length < 1) { this.results = []; return; }
                this.loading = true;
                try {
                    const res = await fetch(`${this.searchUrl}?q=${encodeURIComponent(q)}`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    this.results = res.ok ? await res.json() : [];
                } catch (e) {
                    this.results = [];
                } finally {
                    this.loading = false;
                }
            },

            addToCart(p) {
                if (p.stock < 1) return;
                const existing = this.cart.find(i => i.id === p.id);
                if (existing) {
                    existing.qty = Math.min(existing.qty + 1, existing.stock);
                } else {
                    this.cart.push({
                        id: p.id, name: p.name, sku: p.sku,
                        price: Number(p.price), gst: Number(p.gst || 0),
                        stock: Number(p.stock), qty: 1, discount: 0,
                    });
                }
                this.term = '';
                this.results = [];
                this.$refs.search.focus();
            },

            clampQty(item) {
                if (item.qty > item.stock) item.qty = item.stock;
                if (item.qty < 1 || isNaN(item.qty)) item.qty = 1;
            },

            removeItem(i) { this.cart.splice(i, 1); },

            lineBase(item) {
                return (Number(item.price) || 0) * (Number(item.qty) || 0) * (1 - (Number(item.discount) || 0) / 100);
            },
            lineTax(item) { return this.lineBase(item) * (Number(item.gst) || 0) / 100; },
            lineTotal(item) { return this.lineBase(item) + this.lineTax(item); },

            subtotal() { return this.cart.reduce((s, i) => s + this.lineBase(i), 0); },
            taxTotal() { return this.cart.reduce((s, i) => s + this.lineTax(i), 0); },
            grandTotal() { return Math.max(0, this.subtotal() + this.taxTotal() - (Number(this.headerDiscount) || 0)); },
            due() { return Math.max(0, this.grandTotal() - (Number(this.paidAmount) || 0)); },
            change() { return Math.max(0, (Number(this.paidAmount) || 0) - this.grandTotal()); },

            prepare(e) {
                if (!this.cart.length) { e.preventDefault(); return; }
                this.$refs.itemsJson.value = JSON.stringify(this.cart.map(i => ({
                    product_id: i.id,
                    quantity: i.qty,
                    discount_percentage: i.discount || 0,
                })));
                this.$refs.action.value = this.actionType;
            },
        };
    }
</script>
@endsection
