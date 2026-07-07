@php($expense = $expense ?? null)

@if ($errors->any())
    <div class="rounded border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700">
        <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">Category <span class="text-rose-500">*</span></label>
        <select name="expense_category_id" class="w-full border rounded px-3 py-2">
            <option value="">— Select category —</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    @selected((string) old('expense_category_id', $expense->expense_category_id ?? '') === (string) $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-1">Or add a new one below.</p>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">New Category</label>
        <input type="text" name="new_category" value="{{ old('new_category') }}"
               placeholder="e.g. Electricity, Rent"
               class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Amount (₹) <span class="text-rose-500">*</span></label>
        <input type="number" step="0.01" min="0.01" name="amount"
               value="{{ old('amount', $expense->amount ?? '') }}"
               class="w-full border rounded px-3 py-2" required>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Date <span class="text-rose-500">*</span></label>
        <input type="date" name="date"
               value="{{ old('date', optional($expense->date ?? now())->toDateString()) }}"
               max="{{ now()->toDateString() }}"
               class="w-full border rounded px-3 py-2" required>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Vendor / Paid To</label>
        <input type="text" name="vendor" value="{{ old('vendor', $expense->vendor ?? '') }}"
               class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Receipt (JPG/PNG/PDF, ≤5MB)</label>
        <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf"
               class="w-full border rounded px-3 py-2">
        @if (($expense->receipt_path ?? null))
            <label class="mt-2 inline-flex items-center gap-2 text-xs text-gray-600">
                <input type="checkbox" name="remove_receipt" value="1"> Remove current receipt
            </label>
            <a href="{{ Storage::url($expense->receipt_path) }}" target="_blank"
               class="ml-2 text-xs text-emerald-700 hover:underline">View current</a>
        @endif
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1">Description</label>
        <textarea name="description" rows="3"
                  class="w-full border rounded px-3 py-2">{{ old('description', $expense->description ?? '') }}</textarea>
    </div>
</div>
