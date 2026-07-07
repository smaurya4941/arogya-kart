<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $pharmacyId = auth()->user()->pharmacy_id;
        $settings = Setting::where('pharmacy_id', $pharmacyId)->pluck('value', 'key')->toArray();

        return view('pharmacy.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $pharmacyId = auth()->user()->pharmacy_id;
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['pharmacy_id' => $pharmacyId, 'key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->route('pharmacy.settings.index')->with('success', 'Settings updated successfully.');
    }
}
