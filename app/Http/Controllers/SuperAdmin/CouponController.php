<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Discount codes for subscription billing. This manages the catalogue; the
 * redemption hook lives in the checkout flow (Coupon::isRedeemable/discountFor).
 */
class CouponController extends Controller
{
    public function __construct(
        private readonly AuditLogService $audit
    ) {}

    public function index()
    {
        $coupons = Coupon::latest()->paginate(20);

        return view('superadmin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('superadmin.coupons.create', [
            'coupon' => new Coupon(['type' => Coupon::TYPE_PERCENT, 'is_active' => true]),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $coupon = Coupon::create($data);

        $this->audit->log(auth()->user(), 'coupon_created', $coupon, ['code' => $coupon->code]);

        return redirect()->route('superadmin.coupons.index')->with('success', "Coupon {$coupon->code} created.");
    }

    public function edit(Coupon $coupon)
    {
        return view('superadmin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $coupon->update($this->validateData($request, $coupon));

        $this->audit->log(auth()->user(), 'coupon_updated', $coupon, ['code' => $coupon->code]);

        return redirect()->route('superadmin.coupons.index')->with('success', "Coupon {$coupon->code} updated.");
    }

    public function toggle(Coupon $coupon)
    {
        $coupon->update(['is_active' => ! $coupon->is_active]);

        return back()->with('success', "Coupon {$coupon->code} " . ($coupon->is_active ? 'activated' : 'deactivated') . '.');
    }

    public function destroy(Coupon $coupon)
    {
        // Guard: keep redemption history intact — deactivate a used coupon instead.
        if ($coupon->redeemed_count > 0) {
            return back()->with('error', 'This coupon has been redeemed; deactivate it instead of deleting.');
        }

        $code = $coupon->code;
        $coupon->delete();

        $this->audit->log(auth()->user(), 'coupon_deleted', null, ['code' => $code]);

        return redirect()->route('superadmin.coupons.index')->with('success', "Coupon {$code} deleted.");
    }

    /**
     * @return array<string,mixed>
     */
    private function validateData(Request $request, ?Coupon $coupon = null): array
    {
        $validated = $request->validate([
            'code'            => ['required', 'string', 'max:40', 'alpha_dash', Rule::unique('coupons', 'code')->ignore($coupon?->id)],
            'description'     => ['nullable', 'string', 'max:255'],
            'type'            => ['required', Rule::in([Coupon::TYPE_PERCENT, Coupon::TYPE_FIXED])],
            'value'           => ['required', 'numeric', 'min:0', $request->input('type') === Coupon::TYPE_PERCENT ? 'max:100' : 'max:1000000'],
            'max_redemptions' => ['nullable', 'integer', 'min:1'],
            'expires_at'      => ['nullable', 'date', 'after:now'],
            'is_active'       => ['nullable', 'boolean'],
        ]);

        $validated['code']      = Str::upper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
