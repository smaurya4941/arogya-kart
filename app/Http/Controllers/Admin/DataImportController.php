<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DataImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DataImportController extends Controller
{
    public function create()
    {
        return view('admin.imports.create');
    }

    public function store(Request $request, DataImportService $importService)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'], // Max 10MB
        ]);

        $file = $request->file('csv_file');
        $path = $file->storeAs('imports', 'import_' . time() . '.csv', 'local');
        $fullPath = Storage::disk('local')->path($path);

        try {
            $pharmacyId = auth()->user()->pharmacy_id;
            $result = $importService->importProducts($fullPath, $pharmacyId);
            
            // Optionally delete the file after successful import
            Storage::disk('local')->delete($path);

            if (!empty($result['errors'])) {
                return redirect()->back()->with('warning', 'Import completed with ' . count($result['errors']) . ' errors. Imported: ' . $result['imported']);
            }

            return redirect()->route('admin.products.index')->with('success', "Successfully imported {$result['imported']} products.");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
