<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerPaymentController extends Controller
{
    public function store(Request $request, Customer $customer)
    {
        $this->authorize('update', $customer);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reference' => ['nullable', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($validated, $customer) {
            CustomerLedger::create([
                'pharmacy_id' => $customer->pharmacy_id,
                'customer_id' => $customer->id,
                'type' => 'payment',
                'amount' => -$validated['amount'], // Negative amount for credit/payment
                'reference' => $validated['reference'],
                'date' => $validated['date'],
                'description' => $validated['description'] ?? 'Payment received',
            ]);

            $customer->decrement('outstanding_balance', $validated['amount']);
        });

        return redirect()->back()->with('success', 'Payment recorded successfully.');
    }
}
