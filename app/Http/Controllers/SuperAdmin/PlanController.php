<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::withCount('subscriptions')->orderBy('price_monthly')->get();

        return view('superadmin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('superadmin.plans.create', ['plan' => new Plan()]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        Plan::create($data);

        return redirect()->route('superadmin.plans.index')->with('success', 'Plan created.');
    }

    public function edit(Plan $plan)
    {
        return view('superadmin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $this->validateData($request, $plan);

        $plan->update($data);

        return redirect()->route('superadmin.plans.index')->with('success', 'Plan updated.');
    }

    public function destroy(Plan $plan)
    {
        // Guard: never orphan live subscriptions by hard-deleting a plan in use.
        if ($plan->subscriptions()->exists()) {
            return back()->with('error', 'Cannot delete a plan with existing subscriptions. Deactivate it instead.');
        }

        $plan->delete();

        return back()->with('success', 'Plan deleted.');
    }

    /**
     * @return array<string,mixed>
     */
    private function validateData(Request $request, ?Plan $plan = null): array
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string', 'max:1000'],
            'price_monthly' => ['required', 'numeric', 'min:0'],
            'price_yearly'  => ['required', 'numeric', 'min:0'],
            'max_users'     => ['required', 'integer', 'min:1'],
            'max_branches'  => ['required', 'integer', 'min:1'],
            'api_access'    => ['nullable', 'boolean'],
            'is_active'     => ['nullable', 'boolean'],
            'features'      => ['nullable', 'string'], // newline-separated in the form
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Enforce slug uniqueness (the form doesn't expose it directly).
        $request->merge(['slug' => $validated['slug']]);
        $request->validate([
            'slug' => [Rule::unique('plans', 'slug')->ignore($plan?->id)],
        ]);

        $validated['api_access'] = $request->boolean('api_access');
        $validated['is_active']  = $request->boolean('is_active');

        // Features arrive as one-per-line text; store as a clean JSON array.
        $validated['features'] = collect(preg_split('/\r\n|\r|\n/', (string) $request->input('features')))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();

        return $validated;
    }
}
