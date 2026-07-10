<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-gray-900 dark:text-white leading-tight">
            {{ __('Complete Payment') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-8 border border-gray-100 dark:border-gray-700 text-center">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $plan->name }} Plan</h3>
                <p class="text-gray-500 dark:text-gray-400">{{ ucfirst($cycle) }} billing</p>
                <p class="text-4xl font-extrabold text-gray-900 dark:text-white my-6">₹{{ number_format($amount, 2) }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">+ applicable GST. You'll be redirected to Razorpay's secure checkout.</p>

                <button id="pay-btn" class="w-full py-3 px-4 rounded-xl font-bold bg-blue-600 hover:bg-blue-700 text-white transition-colors">
                    Pay Securely
                </button>

                <a href="{{ route('admin.subscription.index') }}" class="block mt-4 text-sm text-gray-500 hover:underline">Cancel</a>
            </div>

            {{-- Signature fields are POSTed here after Razorpay confirms the payment. --}}
            <form id="callback-form" action="{{ route('admin.subscription.callback') }}" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                <input type="hidden" name="billing_cycle" value="{{ $cycle }}">
                <input type="hidden" name="razorpay_order_id" id="rzp_order_id">
                <input type="hidden" name="razorpay_payment_id" id="rzp_payment_id">
                <input type="hidden" name="razorpay_signature" id="rzp_signature">
            </form>
        </div>
    </div>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        const options = {
            key: @json($razorKey),
            order_id: @json($order['id']),
            amount: @json($order['amount']),
            currency: @json($order['currency']),
            name: @json(config('app.name')),
            description: @json($plan->name . ' — ' . ucfirst($cycle)),
            prefill: {
                name: @json($pharmacy->owner_name),
                email: @json($pharmacy->email),
                contact: @json($pharmacy->phone),
            },
            theme: { color: '#2563eb' },
            handler: function (response) {
                document.getElementById('rzp_order_id').value = response.razorpay_order_id;
                document.getElementById('rzp_payment_id').value = response.razorpay_payment_id;
                document.getElementById('rzp_signature').value = response.razorpay_signature;
                document.getElementById('callback-form').submit();
            },
        };

        const rzp = new Razorpay(options);
        document.getElementById('pay-btn').addEventListener('click', () => rzp.open());
        // Auto-open once the page is ready.
        window.addEventListener('load', () => rzp.open());
    </script>
</x-app-layout>
