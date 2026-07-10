<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Pharmacy;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

/**
 * Platform-owner control tower. Every query here is intentionally cross-tenant —
 * the Super Admin bypasses BelongsToPharmacy, so these figures span all
 * pharmacies on the platform.
 */
class DashboardController extends Controller
{
    public function index()
    {
        $totalPharmacies  = Pharmacy::count();
        $activePharmacies = Pharmacy::where('status', Pharmacy::STATUS_ACTIVE)->count();

        $activeSubs = Subscription::whereIn('status', [
            Subscription::STATUS_TRIAL,
            Subscription::STATUS_ACTIVE,
        ])->count();

        $trialSubs = Subscription::where('status', Subscription::STATUS_TRIAL)->count();

        // Recognised revenue = paid subscription invoices.
        $totalRevenue   = Invoice::where('status', Invoice::STATUS_PAID)->sum('total');
        $monthlyRevenue = Invoice::where('status', Invoice::STATUS_PAID)
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('total');

        $recentPharmacies = Pharmacy::with('currentSubscription.plan')
            ->latest()
            ->take(8)
            ->get();

        // Plan distribution among active subscriptions (for a quick mix chart/table).
        $planDistribution = Subscription::query()
            ->whereIn('status', [Subscription::STATUS_TRIAL, Subscription::STATUS_ACTIVE])
            ->select('plan_id', DB::raw('COUNT(*) as total'))
            ->with('plan')
            ->groupBy('plan_id')
            ->get();

        return view('superadmin.dashboard', compact(
            'totalPharmacies',
            'activePharmacies',
            'activeSubs',
            'trialSubs',
            'totalRevenue',
            'monthlyRevenue',
            'recentPharmacies',
            'planDistribution',
        ));
    }
}
