<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

/**
 * Platform-owner subscription management. Every mutation here is a manual,
 * out-of-band change (comping a plan, extending a trial, cancelling for
 * non-payment, correcting a period end) and is written to the audit trail.
 *
 * Subscription uses BelongsToPharmacy, but the Super Admin bypasses that scope,
 * so these queries and route-model bindings resolve across all tenants.
 */
class SubscriptionController extends Controller
{
    private const STATUSES = [
        Subscription::STATUS_TRIAL,
        Subscription::STATUS_ACTIVE,
        Subscription::STATUS_EXPIRED,
        Subscription::STATUS_CANCELLED,
        Subscription::STATUS_SUSPENDED,
    ];

    private const CYCLES = ['monthly', 'quarterly', 'yearly'];

    public function __construct(
        private readonly AuditLogService $audit
    ) {}

    public function index(Request $request)
    {
        $subscriptions = Subscription::query()
            ->with(['pharmacy', 'plan'])
            ->withCount('invoices')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('plan_id'), fn ($q) => $q->where('plan_id', $request->integer('plan_id')))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('superadmin.subscriptions.index', [
            'subscriptions' => $subscriptions,
            'statuses'      => self::STATUSES,
            'plans'         => Plan::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create()
    {
        return view('superadmin.subscriptions.create', [
            'subscription' => new Subscription(['status' => Subscription::STATUS_TRIAL, 'billing_cycle' => 'monthly']),
            'pharmacies'   => Pharmacy::orderBy('name')->get(['id', 'name']),
            'plans'        => Plan::orderBy('name')->get(),
            'statuses'     => self::STATUSES,
            'cycles'       => self::CYCLES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $subscription = Subscription::create($this->attributesFrom($data));

        $this->audit->log(auth()->user(), 'subscription_created', $subscription, [
            'pharmacy_id' => $subscription->pharmacy_id,
            'plan_id'     => $subscription->plan_id,
            'status'      => $subscription->status,
        ]);

        return redirect()->route('superadmin.subscriptions.index')
            ->with('success', 'Subscription created.');
    }

    public function edit(Subscription $subscription)
    {
        $subscription->load(['pharmacy', 'plan', 'invoices' => fn ($q) => $q->latest()->limit(10)]);

        return view('superadmin.subscriptions.edit', [
            'subscription' => $subscription,
            'pharmacies'   => Pharmacy::orderBy('name')->get(['id', 'name']),
            'plans'        => Plan::orderBy('name')->get(),
            'statuses'     => self::STATUSES,
            'cycles'       => self::CYCLES,
        ]);
    }

    public function update(Request $request, Subscription $subscription)
    {
        $data = $this->validateData($request, $subscription);

        $subscription->update($this->attributesFrom($data));

        $this->audit->log(auth()->user(), 'subscription_updated', $subscription, [
            'plan_id' => $subscription->plan_id,
            'status'  => $subscription->status,
        ]);

        return redirect()->route('superadmin.subscriptions.index')
            ->with('success', 'Subscription updated.');
    }

    /** Extend (or start) the trial window by a number of days. */
    public function extendTrial(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'days' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        // Extend from whichever is later: the existing trial end or now — so a
        // lapsed trial is pushed into the future rather than staying in the past.
        $base = $subscription->trial_ends_at && $subscription->trial_ends_at->isFuture()
            ? $subscription->trial_ends_at
            : now();

        $subscription->update([
            'status'        => Subscription::STATUS_TRIAL,
            'trial_ends_at' => $base->copy()->addDays($validated['days']),
        ]);

        $this->audit->log(auth()->user(), 'subscription_trial_extended', $subscription, [
            'days'          => $validated['days'],
            'trial_ends_at' => $subscription->trial_ends_at?->toDateString(),
        ]);

        return back()->with('success', "Trial extended by {$validated['days']} days.");
    }

    /** Cancel a subscription — access is lost per Subscription::isValid(). */
    public function cancel(Subscription $subscription)
    {
        $subscription->update(['status' => Subscription::STATUS_CANCELLED]);

        $this->audit->log(auth()->user(), 'subscription_cancelled', $subscription, [
            'pharmacy_id' => $subscription->pharmacy_id,
        ]);

        return back()->with('success', 'Subscription cancelled.');
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete(); // soft delete — history is retained

        $this->audit->log(auth()->user(), 'subscription_deleted', $subscription, [
            'pharmacy_id' => $subscription->pharmacy_id,
        ]);

        return redirect()->route('superadmin.subscriptions.index')
            ->with('success', 'Subscription removed.');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<string,mixed>
     */
    private function validateData(Request $request, ?Subscription $subscription = null): array
    {
        return $request->validate([
            'pharmacy_id'   => ['required', 'exists:pharmacies,id'],
            'plan_id'       => ['required', 'exists:plans,id'],
            'status'        => ['required', Rule::in(self::STATUSES)],
            'billing_cycle' => ['required', Rule::in(self::CYCLES)],
            'starts_at'     => ['nullable', 'date'],
            'period_end'    => ['nullable', 'date'],
        ]);
    }

    /**
     * Map the validated form into model attributes. The single "period end" field
     * routes to trial_ends_at for trials and ends_at otherwise, matching how
     * Subscription::currentPeriodEnd() reads them back.
     *
     * @param  array<string,mixed>  $data
     * @return array<string,mixed>
     */
    private function attributesFrom(array $data): array
    {
        $isTrial   = $data['status'] === Subscription::STATUS_TRIAL;
        $periodEnd = ! empty($data['period_end']) ? Carbon::parse($data['period_end'])->endOfDay() : null;

        return [
            'pharmacy_id'   => $data['pharmacy_id'],
            'plan_id'       => $data['plan_id'],
            'status'        => $data['status'],
            'billing_cycle' => $data['billing_cycle'],
            'starts_at'     => ! empty($data['starts_at']) ? Carbon::parse($data['starts_at']) : now(),
            'trial_ends_at' => $isTrial ? $periodEnd : null,
            'ends_at'       => $isTrial ? null : $periodEnd,
        ];
    }
}
