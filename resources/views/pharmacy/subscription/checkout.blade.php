<x-app-layout>
    <div class="page mx-auto max-w-lg">
        <div class="page-header">
            <h1 class="page-title">Complete Payment</h1>
        </div>
        <div class="card card-pad text-center">
            <h3 class="text-xl font-bold text-on-surface">{{ $plan->name }} Plan</h3>
            <p class="text-on-surface-variant">{{ ucfirst($cycle) }} billing</p>
            <p class="my-6 text-4xl font-extrabold text-on-surface">₹{{ number_format($amount, 2) }}</p>
            @if(!empty($couponCode))
                <p class="mb-2 inline-flex items-center gap-1 rounded-full bg-tertiary-container/20 px-3 py-1 text-sm font-medium text-tertiary">
                    <span class="material-symbols-outlined text-[16px]">sell</span> Coupon {{ $couponCode }} applied
                </p>
            @endif
            <p class="mb-8 text-sm text-on-surface-variant">+ applicable GST. You'll be redirected to Razorpay's secure checkout.</p>

            <button id="pay-btn" class="btn btn-primary w-full">Pay Securely</button>

            <a href="{{ route('admin.subscription.index') }}" class="mt-4 block text-sm text-on-surface-variant hover:underline">Cancel</a>
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
            theme: { color: '#00685f' },
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
