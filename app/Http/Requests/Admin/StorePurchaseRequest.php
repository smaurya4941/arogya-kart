<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Policy authorization (PurchasePolicy::create) is enforced in the controller.
        // FormRequest only verifies authentication.
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'supplier_invoice_number' => ['nullable', 'string', 'max:100'],
            'payment_terms' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.batch_number' => ['required', 'string', 'max:100'],
            'items.*.expiry_date' => ['required', 'date', 'after:today'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.purchase_price' => ['required', 'numeric', 'min:0'],
            'items.*.mrp' => ['required', 'numeric', 'min:0', 'gte:items.*.purchase_price'],
            'items.*.selling_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.gst_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Add at least one line item to the purchase.',
            'items.*.product_id.required' => 'Select a product for each line.',
            'items.*.mrp.gte' => 'MRP must be greater than or equal to the purchase price.',
            'items.*.expiry_date.after' => 'Expiry date must be in the future.',
        ];
    }
}
