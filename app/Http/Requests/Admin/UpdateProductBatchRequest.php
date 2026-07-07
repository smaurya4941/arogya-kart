<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'batch_number' => ['required', 'string', 'max:100'],
            'expiry_date' => ['required', 'date'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'mrp' => ['required', 'numeric', 'min:0', 'gte:purchase_price'],
            'quantity' => ['required', 'integer', 'min:0'],
        ];
    }
}
