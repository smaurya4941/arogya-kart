<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Policy authorization (SalePolicy::create) is enforced in the controller
        // via $this->authorize(). The FormRequest only needs to ensure the user
        // is authenticated so staff members can submit POS bills.
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'exists:customers,id'],
            'payment_method' => ['required', 'in:cash,card,upi,credit'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'doctor_name' => ['nullable', 'string', 'max:255'],
            'doctor_registration_number' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);
            if (!is_array($items) || empty($items)) {
                return;
            }

            $productIds = collect($items)->pluck('product_id')->filter()->toArray();
            if (empty($productIds)) {
                return;
            }

            $hasRestrictedDrug = \App\Models\Product::whereIn('id', $productIds)
                ->whereIn('schedule_type', ['H', 'H1', 'X'])
                ->exists();

            if ($hasRestrictedDrug) {
                if (empty($this->input('customer_id'))) {
                    $validator->errors()->add('customer_id', 'Customer profile is required when selling Schedule H/H1/X drugs.');
                }
                if (empty($this->input('doctor_name'))) {
                    $validator->errors()->add('doctor_name', 'Doctor name is required when selling Schedule H/H1/X drugs.');
                }
                if (empty($this->input('doctor_registration_number'))) {
                    $validator->errors()->add('doctor_registration_number', 'Doctor registration number is required when selling Schedule H/H1/X drugs.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Add at least one medicine to the bill.',
            'items.min' => 'Add at least one medicine to the bill.',
            'items.*.product_id.required' => 'Each cart line must reference a product.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // The POS posts the cart as a JSON string; normalise it into an array so
        // the array-based rules above can validate each line.
        if ($this->has('items_json') && ! $this->has('items')) {
            $this->merge([
                'items' => json_decode($this->input('items_json'), true) ?: [],
            ]);
        }
    }
}
