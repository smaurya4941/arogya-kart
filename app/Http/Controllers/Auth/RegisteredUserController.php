<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\TenantProvisioner;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Enums\UserRole;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'pharmacy_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // A public signup provisions a new tenant: one pharmacy and its owner, who
        // becomes that pharmacy's admin. Wrapped in a transaction so we never end up
        // with an orphaned pharmacy or a user without a pharmacy — the exact broken
        // state (pharmacy_id = null) that made the core sales workflow unusable.
        $user = DB::transaction(function () use ($validated) {
            $pharmacy = Pharmacy::create([
                'name' => $validated['pharmacy_name'],
                'owner_name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'status' => Pharmacy::STATUS_ACTIVE,
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => UserRole::ADMIN,
                'pharmacy_id' => $pharmacy->id,
                'status' => 'active',
            ]);

            // Every new tenant starts on a free trial of the entry plan so the app
            // is immediately usable and the subscription gate lets them straight in.
            // Falls back gracefully if plans haven't been seeded yet.
            if ($plan = Plan::active()->orderBy('price_monthly')->first()) {
                $trialDays = (int) config('saas.trial_days', 14);

                Subscription::create([
                    'pharmacy_id'   => $pharmacy->id,
                    'plan_id'       => $plan->id,
                    'status'        => Subscription::STATUS_TRIAL,
                    'billing_cycle' => 'monthly',
                    'starts_at'     => now(),
                    'trial_ends_at' => now()->addDays($trialDays),
                    'ends_at'       => now()->addDays($trialDays),
                ]);
            }

            // Seed baseline reference data (units, expense categories) so the store
            // is immediately usable.
            app(TenantProvisioner::class)->provision($pharmacy);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect('/admin/dashboard');
    }
}
