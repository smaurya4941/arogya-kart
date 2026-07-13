<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use App\Models\User;
use App\Services\AuditLogService;
use App\Support\AdminCapability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Platform-wide user administration. Unlike the tenant TeamController — which is
 * seat-capped and scoped to a single pharmacy — this panel lets the platform
 * owner see and manage every account on the system, across all tenants and all
 * roles (including other Super Admins).
 *
 * The User model does not use BelongsToPharmacy, so these queries are naturally
 * cross-tenant; no scope bypass is needed here. Every mutating action is written
 * to the audit trail so platform-level account changes are accountable.
 */
class UserController extends Controller
{
    /** Status values allowed on the users table (see the status enum migration). */
    private const STATUSES = ['active', 'inactive', 'suspended'];

    public function __construct(
        private readonly AuditLogService $audit
    ) {}

    public function index(Request $request)
    {
        $users = User::query()
            ->with('pharmacy')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%' . $request->string('q') . '%';
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term)
                      ->orWhere('email', 'like', $term)
                      ->orWhere('phone', 'like', $term);
                });
            })
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->string('role')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('pharmacy_id'), fn ($q) => $q->where('pharmacy_id', $request->integer('pharmacy_id')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Headline counts (unfiltered) for the stat strip.
        $roleCounts = User::query()
            ->select('role', DB::raw('COUNT(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        return view('superadmin.users.index', [
            'users'       => $users,
            'roles'       => UserRole::cases(),
            'statuses'    => self::STATUSES,
            'pharmacies'  => Pharmacy::orderBy('name')->get(['id', 'name']),
            'roleCounts'  => $roleCounts,
            'totalUsers'  => User::count(),
        ]);
    }

    public function create()
    {
        return view('superadmin.users.create', [
            'user'       => new User(['status' => 'active']),
            'roles'      => UserRole::cases(),
            'statuses'   => self::STATUSES,
            'pharmacies' => Pharmacy::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        // Only a full platform owner may mint Super Admins — otherwise a restricted
        // (support) admin with the "users" capability could escalate privileges.
        if ($validated['role'] === UserRole::SUPER_ADMIN->value && ! auth()->user()->isFullSuperAdmin()) {
            return back()->withInput()->with('error', 'Only a full platform owner can create Super Admin accounts.');
        }

        $user = User::create([
            'name'               => $validated['name'],
            'email'              => $validated['email'],
            'phone'              => $validated['phone'] ?? null,
            'role'               => $validated['role'],
            'status'             => $validated['status'],
            'pharmacy_id'        => $validated['pharmacy_id'],
            'admin_capabilities' => $this->capabilitiesFrom($request, $validated['role']),
            'password'           => Hash::make($validated['password']),
            // Platform-provisioned accounts are trusted — skip email verification.
            'email_verified_at'  => now(),
        ]);

        $this->audit->log(auth()->user(), 'user_created', $user, [
            'name'        => $user->name,
            'email'       => $user->email,
            'role'        => $user->role?->value,
            'pharmacy_id' => $user->pharmacy_id,
        ]);

        return redirect()->route('superadmin.users.index')
            ->with('success', "User \"{$user->name}\" created.");
    }

    public function edit(User $user)
    {
        return view('superadmin.users.edit', [
            'user'       => $user,
            'roles'      => UserRole::cases(),
            'statuses'   => self::STATUSES,
            'pharmacies' => Pharmacy::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $this->validateData($request, $user);

        // Guard: the acting Super Admin must not demote or lock out their own
        // account, or they could strand the platform without an operator.
        if ($user->id === auth()->id()) {
            if ($validated['role'] !== UserRole::SUPER_ADMIN->value) {
                return back()->withInput()->with('error', 'You cannot change your own role.');
            }
            if ($validated['status'] !== 'active') {
                return back()->withInput()->with('error', 'You cannot deactivate your own account.');
            }
        }

        // Only a full platform owner may create, elevate to, or modify Super Admin
        // accounts (including their capabilities), preventing privilege escalation.
        if (($validated['role'] === UserRole::SUPER_ADMIN->value || $user->isSuperAdmin())
            && ! auth()->user()->isFullSuperAdmin()) {
            return back()->withInput()->with('error', 'Only a full platform owner can manage Super Admin accounts.');
        }

        $user->fill([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'phone'       => $validated['phone'] ?? null,
            'role'        => $validated['role'],
            'status'      => $validated['status'],
            'pharmacy_id' => $validated['pharmacy_id'],
        ]);

        // Capabilities are editable for other Super Admins only. Never let an admin
        // narrow their own capabilities (self-lockout) — keep their existing set.
        if ($user->id !== auth()->id()) {
            $user->admin_capabilities = $this->capabilitiesFrom($request, $validated['role']);
        }

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        $this->audit->log(auth()->user(), 'user_updated', $user, [
            'name'             => $user->name,
            'email'            => $user->email,
            'role'             => $user->role?->value,
            'password_changed' => ! empty($validated['password']),
        ]);

        return redirect()->route('superadmin.users.index')
            ->with('success', "User \"{$user->name}\" updated.");
    }

    /** Suspend or reactivate an account without deleting its history. */
    public function toggleStatus(User $user)
    {
        abort_if($user->id === auth()->id(), 403, 'You cannot change your own status.');

        $user->status = $user->status === 'active' ? 'suspended' : 'active';
        $user->save();

        $this->audit->log(auth()->user(), 'user_status_changed', $user, [
            'name'   => $user->name,
            'status' => $user->status,
        ]);

        return back()->with('success', "\"{$user->name}\" is now {$user->status}.");
    }

    public function destroy(User $user)
    {
        abort_if($user->id === auth()->id(), 403, 'You cannot delete your own account.');

        $name  = $user->name;
        $email = $user->email;
        $user->delete();

        $this->audit->log(auth()->user(), 'user_deleted', null, [
            'deleted_user_name'  => $name,
            'deleted_user_email' => $email,
        ]);

        return redirect()->route('superadmin.users.index')
            ->with('success', "User \"{$name}\" deleted.");
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    /**
     * Shared rules for store/update. A pharmacy is required for tenant-bound
     * roles (admin/staff/client) and forced to null for the platform-level
     * Super Admin, who is not tied to any single tenant.
     *
     * @return array<string,mixed>
     */
    private function validateData(Request $request, ?User $user = null): array
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'phone'       => ['nullable', 'string', 'max:20'],
            'role'        => ['required', Rule::enum(UserRole::class)],
            'status'      => ['required', Rule::in(self::STATUSES)],
            'pharmacy_id' => ['nullable', 'exists:pharmacies,id', Rule::requiredIf(fn () => $request->input('role') !== UserRole::SUPER_ADMIN->value)],
            // Required on create; optional on edit (blank = keep current password).
            'password'    => [$user ? 'nullable' : 'required', 'confirmed', 'min:8'],
            // Granular Super-Admin capabilities (only meaningful for the super_admin role).
            'admin_capabilities'   => ['nullable', 'array'],
            'admin_capabilities.*' => [Rule::in(AdminCapability::all())],
        ]);

        // Super Admins operate above the tenant boundary — never bind them to one.
        if ($validated['role'] === UserRole::SUPER_ADMIN->value) {
            $validated['pharmacy_id'] = null;
        }

        return $validated;
    }

    /**
     * Resolve the capability set to persist. Non-super-admins hold none (null).
     * A super admin marked "full access" also stores null (implicitly all); an
     * explicitly restricted one stores the validated subset of capability keys.
     *
     * @return array<int,string>|null
     */
    private function capabilitiesFrom(Request $request, string $role): ?array
    {
        if ($role !== UserRole::SUPER_ADMIN->value || $request->boolean('admin_full')) {
            return null;
        }

        return array_values(array_intersect(
            AdminCapability::all(),
            (array) $request->input('admin_capabilities', [])
        ));
    }
}
