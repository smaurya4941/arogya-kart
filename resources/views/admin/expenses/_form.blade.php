@php($expense = $expense ?? null)

@if ($errors->any())
    <div class="rounded-lg border border-error/30 bg-error-container/40 p-3 text-sm text-on-error-container">
        <ul class="list-disc space-y-1 pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="form-label">Category <span class="text-error">*</span></label>
        <select name="expense_category_id" class="form-select">
            <option value="">— Select category —</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    @selected((string) old('expense_category_id', $expense->expense_category_id ?? '') === (string) $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        <p class="form-hint">Or add a new one below.</p>
    </div>

    <div>
        <label class="form-label">New Category</label>
        <input type="text" name="new_category" value="{{ old('new_category') }}" placeholder="e.g. Electricity, Rent" class="form-input">
    </div>

    <div>
        <label class="form-label">Amount (₹) <span class="text-error">*</span></label>
        <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount', $expense->amount ?? '') }}" class="form-input" required>
    </div>

    <div>
        <label class="form-label">Date <span class="text-error">*</span></label>
        <input type="date" name="date" value="{{ old('date', optional($expense->date ?? now())->toDateString()) }}" max="{{ now()->toDateString() }}" class="form-input" required>
    </div>

    <div>
        <label class="form-label">Vendor / Paid To</label>
        <input type="text" name="vendor" value="{{ old('vendor', $expense->vendor ?? '') }}" class="form-input">
    </div>

    <div>
        <label class="form-label">Receipt (JPG/PNG/PDF, ≤5MB)</label>
        <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf" class="form-input h-auto py-1.5">
        @if (($expense->receipt_path ?? null))
            <label class="mt-2 inline-flex items-center gap-2 text-xs text-on-surface-variant">
                <input type="checkbox" name="remove_receipt" value="1" class="rounded border-outline-variant text-primary focus:ring-primary/30"> Remove current receipt
            </label>
            <a href="{{ Storage::url($expense->receipt_path) }}" target="_blank" class="ml-2 text-xs font-medium text-primary hover:underline">View current</a>
        @endif
    </div>

    <div class="md:col-span-2">
        <label class="form-label">Description</label>
        <textarea name="description" rows="3" class="form-textarea">{{ old('description', $expense->description ?? '') }}</textarea>
    </div>
</div>
