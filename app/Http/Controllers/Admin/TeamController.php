<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Lets a pharmacy owner manage their team. Every member created here is a
 * tenant-scoped STAFF user (so they land in the staff workspace) tagged with a
 * job position — a Spatie role that drives granular permissions. Seat count is
 * capped by the pharmacy's current plan (Pharmacy::canAddUser()).
 */
class TeamController extends Controller
{
    /** Job positions an owner may assign, mapped to seeded Spatie roles. */
    private const POSITIONS = ['Pharmacist', 'Cashier', 'Staff'];

    public function index()
    {
        $pharmacy = auth()->user()->pharmacy;

        // Tenant-scoped implicitly via the users relationship on the pharmacy.
        $members = $pharmacy->users()
            ->with('roles')
            ->orderByRaw("FIELD(role, 'admin') DESC") // owner(s) first
            ->orderBy('name')
            ->get();

        $plan      = $pharmacy->currentPlan();
        $seatLimit = $plan?->max_users;
        $seatsUsed = $members->count();

        return view('admin.team.index', [
            'members'   => $members,
            'seatLimit' => $seatLimit,
            'seatsUsed' => $seatsUsed,
            'canAdd'    => $pharmacy->canAddUser(),
        ]);
    }

    public function create()
    {
        if (! auth()->user()->pharmacy->canAddUser()) {
            return redirect()->route('admin.team.index')
                ->with('error', 'You have reached the user limit for your plan. Upgrade to add more team members.');
        }

        return view('admin.team.create', ['positions' => self::POSITIONS]);
    }

    public function store(Request $request)
    {
        $this->assertSeatAvailable();

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'position' => ['required', Rule::in(self::POSITIONS)],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $pharmacy = auth()->user()->pharmacy;

        $member = User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'phone'             => $validated['phone'] ?? null,
            'password'          => Hash::make($validated['password']),
            'role'              => UserRole::STAFF,
            'pharmacy_id'       => $pharmacy->id,
            'status'            => 'active',
            // Owner-provisioned accounts are trusted — skip email verification.
            'email_verified_at' => now(),
        ]);

        $member->syncRoles([$validated['position']]);

        return redirect()->route('admin.team.index')
            ->with('success', "{$member->name} added to your team as {$validated['position']}.");
    }

    public function edit(User $user)
    {
        $this->authorizeMember($user);

        return view('admin.team.edit', [
            'member'    => $user,
            'positions' => self::POSITIONS,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeMember($user);

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'    => ['nullable', 'string', 'max:20'],
            'position' => ['required', Rule::in(self::POSITIONS)],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $user->fill([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->syncRoles([$validated['position']]);

        return redirect()->route('admin.team.index')->with('success', "{$user->name}'s details updated.");
    }

    /** Deactivate/reactivate a member without deleting their history. */
    public function toggleStatus(User $user)
    {
        $this->authorizeMember($user);

        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        return back()->with('success', "{$user->name} is now {$user->status}.");
    }

    public function destroy(User $user)
    {
        $this->authorizeMember($user);

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.team.index')->with('success', "{$name} removed from your team.");
    }

    /*
    |--------------------------------------------------------------------------
    | Guards
    |--------------------------------------------------------------------------
    */

    /**
     * A member must belong to the current pharmacy, must not be the acting owner
     * themselves, and must not be another owner (owners aren't managed here). The
     * global tenant scope already prevents cross-pharmacy access, but we assert it
     * explicitly for defence in depth.
     */
    private function authorizeMember(User $user): void
    {
        $actor = auth()->user();

        abort_unless($user->pharmacy_id === $actor->pharmacy_id, 404);
        abort_if($user->id === $actor->id, 403, 'You cannot manage your own account here.');
        abort_if($user->isAdmin(), 403, 'Pharmacy owners cannot be managed from the team page.');
    }

    private function assertSeatAvailable(): void
    {
        if (! auth()->user()->pharmacy->canAddUser()) {
            throw ValidationException::withMessages([
                'seat' => 'You have reached the user limit for your plan. Upgrade to add more team members.',
            ]);
        }
    }
}
