<?php

namespace App\Http\Controllers;

use App\Models\Sale;

class StaffController extends Controller
{
    /**
     * Staff landing page. Shows the member their own till activity for the day
     * and quick links into whatever they're permitted to do (POS, sales history,
     * inventory) — the view gates each action with @can, so a Cashier and a
     * Pharmacist see different tools.
     */
    public function index()
    {
        $userId = auth()->id();

        // Tenant scoping is automatic (BelongsToPharmacy); we further narrow to
        // sales this staff member rang up today.
        $todaySales = Sale::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->get();

        return view('staff.dashboard', [
            'todayRevenue'  => $todaySales->sum('total_amount'),
            'todayInvoices' => $todaySales->count(),
        ]);
    }
}
