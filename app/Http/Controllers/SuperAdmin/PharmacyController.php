<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use Illuminate\Http\Request;

class PharmacyController extends Controller
{
    public function index(Request $request)
    {
        $pharmacies = Pharmacy::query()
            ->with('currentSubscription.plan')
            ->withCount('users')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%' . $request->string('q') . '%';
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term)
                      ->orWhere('email', 'like', $term)
                      ->orWhere('owner_name', 'like', $term);
                });
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('superadmin.pharmacies.index', compact('pharmacies'));
    }

    public function show(Pharmacy $pharmacy)
    {
        $pharmacy->load([
            'currentSubscription.plan',
            'subscriptions.plan',
            'users',
            'invoices' => fn ($q) => $q->latest()->limit(20),
        ]);

        return view('superadmin.pharmacies.show', compact('pharmacy'));
    }

    /**
     * Suspend or reactivate a tenant. A suspended pharmacy is locked out by
     * EnsureSubscriptionActive regardless of its subscription state.
     */
    public function toggleStatus(Pharmacy $pharmacy)
    {
        $pharmacy->status = $pharmacy->isActive()
            ? Pharmacy::STATUS_SUSPENDED
            : Pharmacy::STATUS_ACTIVE;
        $pharmacy->save();

        $verb = $pharmacy->isActive() ? 'reactivated' : 'suspended';

        return back()->with('success', "Pharmacy \"{$pharmacy->name}\" has been {$verb}.");
    }
}
