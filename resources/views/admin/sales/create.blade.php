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
        <div class="mb-4 rounded-lg border border-error/30 bg-error-container/40 p-3 text-sm text-on-error-container">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="page-header mb-4">
        <div>
            <h1 class="page-title">Point of Sale</h1>
            <p class="page-subtitle">Search a medicine (or press <kbd class="rounded border border-outline-variant/50 px-1">F2</kbd>), add to cart, take payment.</p>
        </div>
        <a href="{{ route('admin.sales.index') }}" class="btn btn-outline">All Sales</a>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        {{-- LEFT: search + cart --}}
        <div class="space-y-4 lg:col-span-2">
            {{-- Search box --}}
            <div class="card card-pad relative">
                <label class="form-label">Find medicine</label>
                <input type="text" x-ref="search" x-model="term"
                       @input.debounce.250ms="search()"
                       @keydown.enter.prevent="results.length && addToCart(results[0])"
                       @keydown.escape="results = []"
                       placeholder="Type name, SKU or scan barcode…"
                       class="form-input" autocomplete="off">

                <div x-show="loading" class="absolute right-8 top-10 text-xs text-on-surface-variant">searching…</div>

                <ul x-show="results.length" x-cloak
                    class="absolute left-4 right-4 z-20 mt-1 max-h-72 overflow-y-auto rounded-lg border border-outline-variant/40 bg-white shadow-lg">
                    <template x-for="p in results" :key="p.id">
                        <li @click="addToCart(p)"
                            class="flex cursor-pointer items-center justify-between px-3 py-2 hover:bg-primary/5">
                            <div>
                                <p class="text-sm font-medium" x-text="p.name"></p>
                                <p class="text-xs text-on-surface-variant">
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
                   class="mt-2 text-xs text-on-surface-variant">No in-stock matches.</p>
            </div>

            {{-- Cart --}}
            <div class="card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table-saas">
                        <thead>
                            <tr>
                                <th>Medicine</th>
                                <th class="w-24">Price</th>
                                <th class="w-24">Qty</th>
                                <th class="w-24">Disc %</th>
                                <th class="w-16">GST %</th>
                                <th class="w-28 text-right">Total</th>
                                <th class="w-8"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, i) in cart" :key="item.id">
                                <tr class="align-middle">
                                    <td>
                                        <p class="font-medium" x-text="item.name"></p>
                                        <p class="text-xs text-on-surface-variant">
                                            <span x-text="item.sku"></span> ·
                                            <span x-text="'in stock: ' + item.stock"></span>
                                        </p>
                                    </td>
                                    <td>₹<span x-text="Number(item.price).toFixed(2)"></span></td>
                                    <td>
                                        <input type="number" min="1" :max="item.stock" x-model.number="item.qty"
                                               @input="clampQty(item)" class="form-input h-8 w-20">
                                    </td>
                                    <td>
                                        <input type="number" min="0" max="100" step="0.01" x-model.number="item.discount"
                                               class="form-input h-8 w-20">
                                    </td>
                                    <td class="text-on-surface-variant" x-text="Number(item.gst).toFixed(0)"></td>
                                    <td class="text-right font-medium">₹<span x-text="lineTotal(item).toFixed(2)"></span></td>
                                    <td>
                                        <button type="button" @click="removeItem(i)" class="btn-icon hover:text-error">✕</button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="!cart.length">
                                <td colspan="7">
                                    <div class="empty-state">
                                        <span class="material-symbols-outlined text-[32px] opacity-40">shopping_cart</span>
                                        Cart is empty. Search to add medicines.
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- RIGHT: checkout --}}
        <div class="space-y-4">
            <form method="POST" action="{{ route('admin.sales.store') }}" @submit="prepare($event)"
                  class="card card-pad space-y-4">
                @csrf
                <input type="hidden" name="items_json" x-ref="itemsJson">
                <input type="hidden" name="action" x-ref="action">

                <div>
                    <label class="form-label">Customer</label>
                    <select name="customer_id" x-model="customerId" class="form-select">
                        <option value="">Walk-in customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">
                                {{ $customer->name }}{{ $customer->phone ? ' — '.$customer->phone : '' }}
                            </option>
                        @endforeach
                    </select>
                    <a href="{{ route('admin.customers.create') }}" target="_blank"
                       class="mt-1 inline-block text-xs font-medium text-primary hover:underline">+ Add new customer</a>
                </div>

                <div>
                    <label class="form-label">Payment Method</label>
                    <select name="payment_method" x-model="paymentMethod" class="form-select">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="upi">UPI</option>
                        <option value="credit">Credit (pay later)</option>
                    </select>
                </div>

                <div class="space-y-2 border-t border-outline-variant/30 pt-3 text-sm">
                    <div class="flex justify-between"><span class="text-on-surface-variant">Subtotal</span><span>₹<span x-text="subtotal().toFixed(2)"></span></span></div>
                    <div class="flex justify-between"><span class="text-on-surface-variant">GST</span><span>₹<span x-text="taxTotal().toFixed(2)"></span></span></div>
                    <div class="flex items-center justify-between">
                        <span class="text-on-surface-variant">Bill Discount ₹</span>
                        <input type="number" min="0" step="0.01" name="discount_amount" x-model.number="headerDiscount"
                               class="form-input h-8 w-28 text-right">
                    </div>
                    <div class="flex justify-between border-t border-outline-variant/30 pt-2 text-lg font-bold">
                        <span>Grand Total</span><span>₹<span x-text="grandTotal().toFixed(2)"></span></span>
                    </div>
                </div>

                <div class="space-y-2 border-t border-outline-variant/30 pt-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-on-surface-variant">Amount Paid ₹</span>
                        <input type="number" min="0" step="0.01" name="paid_amount" x-model.number="paidAmount"
                               @input="paidTouched = true" class="form-input h-8 w-28 text-right">
                    </div>
                    <div class="flex justify-between" x-show="change() > 0" x-cloak>
                        <span class="text-on-surface-variant">Change to return</span><span class="font-semibold text-tertiary">₹<span x-text="change().toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between" x-show="due() > 0" x-cloak>
                        <span class="text-on-surface-variant">Balance due</span><span class="font-semibold text-error">₹<span x-text="due().toFixed(2)"></span></span>
                    </div>
                </div>

                <div>
                    <label class="form-label">Notes</label>
                    <input type="text" name="notes" x-model="notes" class="form-input" placeholder="Optional">
                </div>

                <div class="grid grid-cols-2 gap-2 pt-2">
                    <button type="submit" @click="actionType = 'save'" :disabled="!cart.length" class="btn btn-primary">Save Bill</button>
                    <button type="submit" @click="actionType = 'save_print'" :disabled="!cart.length" class="btn btn-outline">Save &amp; Print</button>
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
