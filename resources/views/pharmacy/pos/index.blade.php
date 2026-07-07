<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS - Arogya Kart</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 h-screen overflow-hidden flex flex-col" x-data="posSystem()">
    
    <!-- Navbar -->
    <header class="bg-white dark:bg-gray-800 shadow-sm z-20 border-b border-gray-200 dark:border-gray-700 h-16 flex items-center justify-between px-4 lg:px-6 shrink-0">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>
                <span class="text-lg font-bold text-gray-900 dark:text-white tracking-tight">Arogya POS</span>
            </div>
        </div>
        <div class="flex items-center space-x-6">
            <div class="hidden md:flex items-center space-x-2 text-sm font-medium text-gray-600 dark:text-gray-300">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span>{{ auth()->user()->name }} (Cashier)</span>
            </div>
            <button class="text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </button>
        </div>
    </header>

    <!-- Main POS Layout -->
    <div class="flex-1 flex flex-col lg:flex-row overflow-hidden">
        
        <!-- Left Panel: Search & Cart -->
        <div class="flex-1 flex flex-col bg-gray-50 dark:bg-gray-900 w-full lg:w-2/3 border-r border-gray-200 dark:border-gray-700">
            <!-- Search Bar -->
            <div class="p-4 bg-white dark:bg-gray-800 shadow-sm z-10 shrink-0">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" x-model="searchQuery" class="block w-full pl-12 pr-3 py-4 border border-gray-300 dark:border-gray-600 rounded-2xl leading-5 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-lg transition-shadow shadow-inner" placeholder="Scan barcode or search medicine name (Alt + S) ...">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <kbd class="hidden sm:inline-flex items-center px-2 py-1 border border-gray-200 dark:border-gray-600 rounded text-xs font-sans font-medium text-gray-400 dark:text-gray-500">Alt S</kbd>
                    </div>
                </div>
            </div>

            <!-- Cart Table Area -->
            <div class="flex-1 overflow-auto bg-white dark:bg-gray-800/50 p-4">
                <template x-if="cart.length === 0">
                    <div class="h-full flex flex-col items-center justify-center text-gray-400 dark:text-gray-500 space-y-4">
                        <div class="w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <p class="text-lg font-medium">Cart is empty</p>
                        <p class="text-sm">Scan a product or search to add items.</p>
                    </div>
                </template>

                <template x-if="cart.length > 0">
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Item</th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Qty</th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Price</th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                <template x-for="(item, index) in cart" :key="index">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="ml-4">
                                                    <div class="text-sm font-bold text-gray-900 dark:text-white" x-text="item.name"></div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">Batch: <span x-text="item.batch"></span> | Exp: <span x-text="item.expiry"></span></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <button @click="decreaseQty(index)" class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 flex items-center justify-center text-gray-600 dark:text-gray-300 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                                </button>
                                                <input type="number" x-model="item.qty" class="w-16 text-center border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <button @click="increaseQty(index)" class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 flex items-center justify-center text-gray-600 dark:text-gray-300 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 dark:text-white font-medium" x-text="'₹' + item.price.toFixed(2)"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900 dark:text-white" x-text="'₹' + (item.price * item.qty).toFixed(2)"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="removeItem(index)" class="text-red-500 hover:text-red-700 bg-red-50 dark:bg-red-900/30 p-2 rounded-lg transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>
        </div>

        <!-- Right Panel: Summary & Payment -->
        <div class="w-full lg:w-1/3 bg-white dark:bg-gray-800 flex flex-col shadow-[-10px_0_15px_-3px_rgba(0,0,0,0.05)] dark:shadow-none z-10 shrink-0">
            
            <!-- Customer Section -->
            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Customer Details
                    </h3>
                    <button class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">+ New</button>
                </div>
                <div class="relative">
                    <input type="text" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Search customer by name or phone...">
                </div>
            </div>

            <!-- Calculation Summary -->
            <div class="p-6 flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-800/50">
                <div class="space-y-4">
                    <div class="flex justify-between items-center text-gray-600 dark:text-gray-400">
                        <span>Subtotal</span>
                        <span class="font-medium text-gray-900 dark:text-white" x-text="'₹' + subtotal.toFixed(2)"></span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400 flex items-center">
                            Discount
                            <button class="ml-2 text-blue-500 hover:text-blue-700"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                        </span>
                        <span class="font-medium text-red-500" x-text="'- ₹' + discount.toFixed(2)"></span>
                    </div>

                    <div class="flex justify-between items-center text-gray-600 dark:text-gray-400">
                        <span>GST Tax</span>
                        <span class="font-medium text-gray-900 dark:text-white" x-text="'₹' + tax.toFixed(2)"></span>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <span class="text-xl font-bold text-gray-900 dark:text-white">Total Amount</span>
                        <span class="text-3xl font-extrabold text-blue-600 dark:text-blue-400" x-text="'₹' + total.toFixed(2)"></span>
                    </div>
                </div>
            </div>

            <!-- Payment Methods & Actions -->
            <div class="p-6 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shrink-0">
                <div class="grid grid-cols-3 gap-3 mb-6">
                    <button @click="paymentMethod = 'cash'" :class="{'ring-2 ring-blue-500 bg-blue-50 dark:bg-blue-900/30 border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-400': paymentMethod === 'cash', 'bg-white dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600': paymentMethod !== 'cash'}" class="border rounded-xl py-3 px-4 flex flex-col items-center justify-center transition-all">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span class="text-sm font-semibold">Cash</span>
                    </button>
                    <button @click="paymentMethod = 'upi'" :class="{'ring-2 ring-blue-500 bg-blue-50 dark:bg-blue-900/30 border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-400': paymentMethod === 'upi', 'bg-white dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600': paymentMethod !== 'upi'}" class="border rounded-xl py-3 px-4 flex flex-col items-center justify-center transition-all">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        <span class="text-sm font-semibold">UPI</span>
                    </button>
                    <button @click="paymentMethod = 'card'" :class="{'ring-2 ring-blue-500 bg-blue-50 dark:bg-blue-900/30 border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-400': paymentMethod === 'card', 'bg-white dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600': paymentMethod !== 'card'}" class="border rounded-xl py-3 px-4 flex flex-col items-center justify-center transition-all">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        <span class="text-sm font-semibold">Card</span>
                    </button>
                </div>

                <button @click="checkout" :disabled="cart.length === 0" class="w-full flex items-center justify-center py-4 px-8 border border-transparent rounded-xl shadow-lg text-lg font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transform transition-all hover:-translate-y-1">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    Print & Complete Bill
                </button>
            </div>
        </div>
    </div>

    <script>
        // AlpineJS logic for the POS
        function posSystem() {
            return {
                searchQuery: '',
                paymentMethod: 'cash',
                cart: [
                    // Dummy data to show UI
                    { id: 1, name: 'Paracetamol 500mg', batch: 'PRC23A', expiry: '12/2026', price: 25.50, qty: 2 },
                    { id: 2, name: 'Azithromycin 250mg', batch: 'AZT11B', expiry: '08/2025', price: 120.00, qty: 1 }
                ],
                get subtotal() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                },
                get discount() {
                    return 0; // Logic for discounts
                },
                get tax() {
                    return this.subtotal * 0.12; // Flat 12% GST example
                },
                get total() {
                    return this.subtotal - this.discount + this.tax;
                },
                increaseQty(index) {
                    this.cart[index].qty++;
                },
                decreaseQty(index) {
                    if(this.cart[index].qty > 1) {
                        this.cart[index].qty--;
                    }
                },
                removeItem(index) {
                    this.cart.splice(index, 1);
                },
                checkout() {
                    alert('Simulating checkout processing...');
                    this.cart = [];
                }
            }
        }
    </script>
</body>
</html>
