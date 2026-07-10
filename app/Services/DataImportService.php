<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataImportService
{
    /**
     * Parse and import a CSV file containing products and opening stock.
     * Expected CSV headers: sku, name, generic_name, manufacturer, category_id, purchase_price, selling_price, tax_percentage, batch_number, expiry_date, batch_qty, schedule_type
     * 
     * @param string $filePath
     * @param int $pharmacyId
     * @return array
     */
    public function importProducts(string $filePath, int $pharmacyId): array
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception('Could not read the uploaded CSV file.');
        }

        $headers = fgetcsv($handle, 1000, ',');
        if (!$headers) {
            throw new \Exception('CSV file is empty or invalid.');
        }

        // Clean headers (remove BOM if exists)
        $headers[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $headers[0]);
        $headers = array_map('strtolower', array_map('trim', $headers));

        $expectedHeaders = ['sku', 'name'];
        foreach ($expectedHeaders as $expected) {
            if (!in_array($expected, $headers)) {
                throw new \Exception("Missing required column: {$expected}");
            }
        }

        $rowCount = 0;
        $successCount = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $rowCount++;
                
                // Skip empty lines
                if (count(array_filter($row)) === 0) continue;

                $data = array_combine($headers, $row);

                if (empty($data['sku']) || empty($data['name'])) {
                    $errors[] = "Row {$rowCount}: Missing SKU or Name.";
                    continue;
                }

                $product = Product::updateOrCreate(
                    [
                        'pharmacy_id' => $pharmacyId,
                        'sku' => $data['sku']
                    ],
                    [
                        'name' => $data['name'],
                        'generic_name' => $data['generic_name'] ?? null,
                        'category_id' => !empty($data['category_id']) ? (int) $data['category_id'] : null,
                        'purchase_price' => !empty($data['purchase_price']) ? (float) $data['purchase_price'] : 0,
                        'selling_price' => !empty($data['selling_price']) ? (float) $data['selling_price'] : 0,
                        'tax_percentage' => !empty($data['tax_percentage']) ? (float) $data['tax_percentage'] : 0,
                        'schedule_type' => !empty($data['schedule_type']) ? strtoupper($data['schedule_type']) : null,
                        'is_active' => true,
                    ]
                );

                // If batch data is provided, create opening stock
                if (!empty($data['batch_number']) && !empty($data['batch_qty'])) {
                    ProductBatch::updateOrCreate(
                        [
                            'pharmacy_id' => $pharmacyId,
                            'product_id' => $product->id,
                            'batch_number' => $data['batch_number'],
                        ],
                        [
                            'supplier_id' => null, // Opening stock might not have a linked supplier here
                            'quantity_received' => (int) $data['batch_qty'],
                            'quantity_remaining' => (int) $data['batch_qty'],
                            'purchase_price' => !empty($data['purchase_price']) ? (float) $data['purchase_price'] : 0,
                            'selling_price' => !empty($data['selling_price']) ? (float) $data['selling_price'] : 0,
                            'expiry_date' => !empty($data['expiry_date']) ? date('Y-m-d', strtotime($data['expiry_date'])) : null,
                            'mrp' => !empty($data['selling_price']) ? (float) $data['selling_price'] : 0,
                        ]
                    );
                }

                $successCount++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("CSV Import Error: " . $e->getMessage());
            throw new \Exception("An error occurred during import at row {$rowCount}. Transaction rolled back.");
        } finally {
            fclose($handle);
        }

        return [
            'total_rows' => $rowCount,
            'imported' => $successCount,
            'errors' => $errors,
        ];
    }
}
