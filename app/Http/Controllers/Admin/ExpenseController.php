<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DashboardController;
use App\Http\Requests\Admin\StoreExpenseRequest;
use App\Http\Requests\Admin\UpdateExpenseRequest;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Expense::class);

        [$start, $end] = $this->range($request);

        $expenses = Expense::query()
            ->with('category')
            ->when($request->integer('category'), fn ($q, $id) => $q->where('expense_category_id', $id))
            ->when($request->string('q')->toString(), function ($query, $q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('vendor', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->whereBetween('date', [$start, $end])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $total = (float) Expense::query()
            ->when($request->integer('category'), fn ($q, $id) => $q->where('expense_category_id', $id))
            ->whereBetween('date', [$start, $end])
            ->sum('amount');

        $categories = ExpenseCategory::orderBy('name')->get();

        return view('admin.expenses.index', compact('expenses', 'categories', 'total', 'start', 'end'));
    }

    public function create()
    {
        $this->authorize('create', Expense::class);

        $categories = ExpenseCategory::orderBy('name')->get();

        return view('admin.expenses.create', compact('categories'));
    }

    public function store(StoreExpenseRequest $request)
    {
        $this->authorize('create', Expense::class);

        $data = $request->validated();
        $data['expense_category_id'] = $this->resolveCategoryId($request);

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        unset($data['new_category'], $data['receipt']);

        $expense = Expense::create($data);
        $this->flushDashboard();

        return redirect()
            ->route('admin.expenses.show', $expense)
            ->with('success', 'Expense recorded successfully.');
    }

    public function show(Expense $expense)
    {
        $this->authorize('view', $expense);

        $expense->load('category');

        return view('admin.expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $this->authorize('update', $expense);

        $categories = ExpenseCategory::orderBy('name')->get();

        return view('admin.expenses.edit', compact('expense', 'categories'));
    }

    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        $this->authorize('update', $expense);

        $data = $request->validated();
        $data['expense_category_id'] = $this->resolveCategoryId($request);

        if ($request->hasFile('receipt')) {
            $this->deleteReceipt($expense);
            $data['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        } elseif ($request->boolean('remove_receipt')) {
            $this->deleteReceipt($expense);
            $data['receipt_path'] = null;
        }

        unset($data['new_category'], $data['receipt'], $data['remove_receipt']);

        $expense->update($data);
        $this->flushDashboard();

        return redirect()
            ->route('admin.expenses.show', $expense)
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $this->authorize('delete', $expense);

        $this->deleteReceipt($expense);
        $expense->delete();
        $this->flushDashboard();

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Resolve the category from an existing selection or a newly typed name,
     * scoping the find-or-create to the current pharmacy.
     */
    private function resolveCategoryId(Request $request): int
    {
        if ($name = trim((string) $request->input('new_category'))) {
            return ExpenseCategory::firstOrCreate(['name' => $name])->id;
        }

        return (int) $request->input('expense_category_id');
    }

    private function deleteReceipt(Expense $expense): void
    {
        if ($expense->receipt_path && Storage::disk('public')->exists($expense->receipt_path)) {
            Storage::disk('public')->delete($expense->receipt_path);
        }
    }

    private function flushDashboard(): void
    {
        DashboardController::flushStats(auth()->user()->pharmacy_id);
    }

    private function range(Request $request): array
    {
        $start = $request->filled('from')
            ? \Illuminate\Support\Carbon::parse($request->query('from'))->startOfDay()
            : now()->startOfMonth();
        $end = $request->filled('to')
            ? \Illuminate\Support\Carbon::parse($request->query('to'))->endOfDay()
            : now()->endOfDay();

        return [$start, $end];
    }
}
