<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    public function index()
    {
        $pharmacyId = auth()->user()->pharmacy_id;
        $currentSubscription = Subscription::with('plan')->where('pharmacy_id', $pharmacyId)->latest()->first();
        $plans = Plan::where('is_active', true)->get();

        return view('pharmacy.subscription.index', compact('currentSubscription', 'plans'));
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly'
        ]);

        $pharmacyId = auth()->user()->pharmacy_id;
        
        // Simplified Logic: Cancel previous active subscriptions and start a new one
        Subscription::where('pharmacy_id', $pharmacyId)->where('status', 'active')->update(['status' => 'cancelled']);

        $plan = Plan::findOrFail($request->plan_id);

        Subscription::create([
            'pharmacy_id' => $pharmacyId,
            'plan_id' => $plan->id,
            'status' => 'active',
            'billing_cycle' => $request->billing_cycle,
            'starts_at' => now(),
            'ends_at' => $request->billing_cycle === 'yearly' ? now()->addYear() : now()->addMonth(),
        ]);

        return redirect()->route('admin.subscription.index')->with('success', 'Successfully subscribed to ' . $plan->name);
    }
}
