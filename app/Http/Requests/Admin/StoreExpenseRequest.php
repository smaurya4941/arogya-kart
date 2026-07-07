<?php

namespace App\Http\Requests\Admin;

use App\Models\ExpenseCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            // Either pick an existing category or type a brand-new one.
            'expense_category_id' => [
                'nullable',
                'required_without:new_category',
                Rule::exists('expense_categories', 'id')->where(
                    fn ($query) => $query->where('pharmacy_id', auth()->user()->pharmacy_id)
                ),
            ],
            'new_category' => ['nullable', 'required_without:expense_category_id', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'expense_category_id' => 'category',
            'new_category' => 'new category',
        ];
    }
}
