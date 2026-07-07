<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pharmacy;
use Illuminate\Support\Facades\Storage;

class PharmacyProfileController extends Controller
{
    public function edit()
    {
        $pharmacy = auth()->user()->pharmacy;
        
        // If the user doesn't have a pharmacy yet, we can either create a dummy one or redirect.
        // Assuming they have one assigned during registration.
        if (!$pharmacy) {
            return redirect()->route('dashboard')->with('error', 'No pharmacy associated with your account.');
        }

        return view('pharmacy.profile.edit', compact('pharmacy'));
    }

    public function update(Request $request)
    {
        $pharmacy = auth()->user()->pharmacy;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'drug_license_number' => 'nullable|string|max:255',
            'gst' => 'nullable|string|max:255',
            'pan_number' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'pincode' => 'nullable|string|max:20',
            'invoice_header' => 'nullable|string|max:255',
            'footer_text' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048'
        ]);

        if ($request->hasFile('logo')) {
            if ($pharmacy->logo_path) {
                Storage::disk('public')->delete($pharmacy->logo_path);
            }
            $path = $request->file('logo')->store('pharmacy_logos', 'public');
            $validated['logo_path'] = $path;
        }

        $pharmacy->update($validated);

        return redirect()->route('pharmacy.profile.edit')->with('success', 'Pharmacy profile updated successfully.');
    }
}
