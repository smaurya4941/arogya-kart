<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\TenantProvisioner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Platform-owner tenant lifecycle: onboard a new pharmacy (with its owner account
 * and starting trial), edit its profile, suspend/reactivate, and soft-delete /
 * restore. Pharmacy uses SoftDeletes, so "deleting" a tenant locks it out (its
 * users fail the subscription gate) while retaining all data for a later restore.
 *
 * The Super Admin bypasses BelongsToPharmacy scoping, so these queries and
 * route-model bindings resolve across all tenants. Every mutation is audited.
 */
class PharmacyController extends Controller
{
    public function __construct(
        private readonly AuditLogService $audit
    ) {}

    public function index(Request $request)
    {
        $trashed = $request->string('trashed')->toString();

        $pharmacies = Pharmacy::query()
            ->when($trashed === 'only', fn ($q) => $q->onlyTrashed())
            ->when($trashed === 'with', fn ($q) => $q->withTrashed())
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

    public function create()
    {
        return view('superadmin.pharmacies.create', [
            'pharmacy'  => new Pharmacy(['status' => Pharmacy::STATUS_ACTIVE]),
            'plans'     => Plan::active()->orderBy('price_monthly')->get(),
            'trialDays' => (int) config('saas.trial_days', 14),
        ]);
    }

    /**
     * Provision a whole tenant in one shot: the pharmacy, its owner (an admin
     * account), the baseline reference data, and an optional starting trial —
     * mirroring public signup (RegisteredUserController) but driven by the
     * platform owner. Wrapped in a transaction so a failure never leaves an
     * orphaned pharmacy or an owner without a tenant.
     */
    public function store(Request $request, TenantProvisioner $provisioner)
    {
        $data = $this->validateProfile($request, null, true);

        $pharmacy = DB::transaction(function () use ($data, $request, $provisioner) {
            $pharmacy = Pharmacy::create(collect($data)->except([
                'owner_name', 'owner_email', 'owner_password', 'plan_id', 'trial_days',
            ])->merge([
                'owner_name' => $data['owner_name'],
                'email'      => $data['owner_email'],
            ])->all());

            User::create([
                'name'              => $data['owner_name'],
                'email'             => $data['owner_email'],
                'phone'             => $data['phone'] ?? null,
                'role'              => UserRole::ADMIN,
                'status'            => 'active',
                'pharmacy_id'       => $pharmacy->id,
                'password'          => Hash::make($data['owner_password']),
                'email_verified_at' => now(), // platform-provisioned owner is trusted
            ]);

            // Optional starting trial on the chosen plan, so the tenant lands with
            // working access exactly like a public signup.
            if (! empty($data['plan_id'])) {
                $trialDays = (int) ($data['trial_days'] ?? config('saas.trial_days', 14));

                Subscription::create([
                    'pharmacy_id'   => $pharmacy->id,
                    'plan_id'       => $data['plan_id'],
                    'status'        => Subscription::STATUS_TRIAL,
                    'billing_cycle' => 'monthly',
                    'starts_at'     => now(),
                    'trial_ends_at' => now()->addDays($trialDays),
                    'ends_at'       => now()->addDays($trialDays),
                ]);
            }

            $provisioner->provision($pharmacy);

            return $pharmacy;
        });

        $this->audit->log(auth()->user(), 'pharmacy_created', $pharmacy, [
            'pharmacy_name' => $pharmacy->name,
            'owner_email'   => $data['owner_email'],
            'plan_id'       => $data['plan_id'] ?? null,
        ]);

        return redirect()->route('superadmin.pharmacies.show', $pharmacy)
            ->with('success', "Pharmacy \"{$pharmacy->name}\" onboarded.");
    }

    public function edit(Pharmacy $pharmacy)
    {
        return view('superadmin.pharmacies.edit', compact('pharmacy'));
    }

    public function update(Request $request, Pharmacy $pharmacy)
    {
        $data = $this->validateProfile($request, $pharmacy, false);

        $pharmacy->update($data);

        $this->audit->log(auth()->user(), 'pharmacy_updated', $pharmacy, [
            'pharmacy_name' => $pharmacy->name,
        ]);

        return redirect()->route('superadmin.pharmacies.show', $pharmacy)
            ->with('success', 'Pharmacy profile updated.');
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

        $this->audit->log(auth()->user(), 'pharmacy_status_changed', $pharmacy, [
            'pharmacy_name' => $pharmacy->name,
            'status'        => $pharmacy->status,
        ]);

        return back()->with('success', "Pharmacy \"{$pharmacy->name}\" has been {$verb}.");
    }

    /** Soft-delete (archive) a tenant. Data is retained and can be restored. */
    public function destroy(Pharmacy $pharmacy)
    {
        $pharmacy->delete();

        $this->audit->log(auth()->user(), 'pharmacy_deleted', $pharmacy, [
            'pharmacy_name' => $pharmacy->name,
        ]);

        return redirect()->route('superadmin.pharmacies.index')
            ->with('success', "Pharmacy \"{$pharmacy->name}\" archived. It can be restored.");
    }

    /** Bring an archived tenant back into service. */
    public function restore(int $pharmacy)
    {
        $model = Pharmacy::onlyTrashed()->findOrFail($pharmacy);
        $model->restore();

        $this->audit->log(auth()->user(), 'pharmacy_restored', $model, [
            'pharmacy_name' => $model->name,
        ]);

        return redirect()->route('superadmin.pharmacies.show', $model)
            ->with('success', "Pharmacy \"{$model->name}\" restored.");
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    /**
     * Shared profile rules. On create ($withOwner) the form also carries the owner
     * account credentials and an optional starting plan; on edit only the pharmacy
     * profile is editable (the owner is managed via the Users panel).
     *
     * @return array<string,mixed>
     */
    private function validateProfile(Request $request, ?Pharmacy $pharmacy, bool $withOwner): array
    {
        $rules = [
            'name'                => ['required', 'string', 'max:255'],
            'owner_name'          => ['required', 'string', 'max:255'],
            'phone'               => ['nullable', 'string', 'max:20'],
            'gst'                 => ['nullable', 'string', 'max:20'],
            'drug_license_number' => ['nullable', 'string', 'max:100'],
            'pan_number'          => ['nullable', 'string', 'max:20'],
            'address'             => ['nullable', 'string', 'max:1000'],
            'city'                => ['nullable', 'string', 'max:100'],
            'state'               => ['nullable', 'string', 'max:100'],
            'pincode'             => ['nullable', 'string', 'max:12'],
        ];

        if ($withOwner) {
            // The owner's email doubles as the pharmacy contact and the login, so it
            // must be unique across all user accounts.
            $rules['owner_email']    = ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')];
            $rules['owner_password'] = ['required', 'confirmed', 'min:8'];
            $rules['plan_id']        = ['nullable', 'exists:plans,id'];
            $rules['trial_days']     = ['nullable', 'integer', 'min:1', 'max:365'];
        } else {
            // On edit the pharmacy keeps its own contact email, independent of the
            // owner user record.
            $rules['email'] = ['nullable', 'string', 'email', 'max:255'];
            $rules['status'] = ['required', Rule::in([Pharmacy::STATUS_ACTIVE, Pharmacy::STATUS_SUSPENDED])];
        }

        return $request->validate($rules);
    }
}
