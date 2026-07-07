<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Expense;
use App\Models\PurchaseInvoice;

class DashboardController extends Controller
{
    /**
     * How long dashboard aggregates stay cached. Writes that move the numbers
     * (sales, purchases, expenses) call flushStats() to invalidate immediately,
     * so this TTL is just a safety net for anything not explicitly flushed.
     */
    private const CACHE_TTL = 600; // 10 minutes

    public function index()
    {
        $pharmacyId = auth()->user()->pharmacy_id;

        $stats = Cache::remember(
            self::cacheKey($pharmacyId),
            self::CACHE_TTL,
            fn () => $this->computeStats(),
        );

        return view('pharmacy.dashboard', $stats);
    }

    /**
     * Build every figure the dashboard renders. Kept private and pure so the
     * result can be memoised wholesale by index().
     */
    private function computeStats(): array
    {
        // Total Medicines
        $totalMedicinesCount = Product::count();
        $activeMedicines = Product::where('status', 'active')->count();
        $inactiveMedicines = $totalMedicinesCount - $activeMedicines;

        // Today's Sales
        $todaySales = Sale::whereDate('created_at', today())->get();
        $todayRevenue = $todaySales->sum('total_amount');
        $todayInvoices = $todaySales->count();
        $todayItemsSold = SaleItem::whereIn('sale_id', $todaySales->pluck('id'))->sum('quantity');

        // Low stock calculation (total stock <= min_stock_alert)
        $lowStockMedicines = Product::whereHas('batches')
            ->get()
            ->filter(fn ($product) => $product->total_stock <= $product->min_stock_alert)
            ->values();
        $lowStockCount = $lowStockMedicines->count();

        // Expiring medicines (next 90 days)
        $expiringMedicines = ProductBatch::where('quantity', '>', 0)
            ->whereBetween('expiry_date', [now(), now()->addDays(90)])
            ->orderBy('expiry_date', 'asc')
            ->get();
        $expiringCount = $expiringMedicines->count();

        // Financial Summary (current month)
        $monthlyRevenue = Sale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');
        $monthlyExpenses = Expense::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');
        $netProfit = $monthlyRevenue - $monthlyExpenses;

        // Recent Activities
        $recentSales = Sale::with(['customer', 'cashier'])->latest()->take(5)->get();
        $recentPurchases = PurchaseInvoice::with('supplier')->latest()->take(5)->get();

        return compact(
            'totalMedicinesCount',
            'activeMedicines',
            'inactiveMedicines',
            'todayRevenue',
            'todayInvoices',
            'todayItemsSold',
            'lowStockMedicines',
            'lowStockCount',
            'expiringMedicines',
            'expiringCount',
            'monthlyRevenue',
            'monthlyExpenses',
            'netProfit',
            'recentSales',
            'recentPurchases'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Cache management
    |--------------------------------------------------------------------------
    */

    public static function cacheKey(int|string|null $pharmacyId): string
    {
        return 'dashboard.stats.pharmacy.' . ($pharmacyId ?? 'none');
    }

    /**
     * Invalidate the cached dashboard for a pharmacy. Call this from any write
     * that changes sales, purchases, stock or expenses so figures stay live.
     */
    public static function flushStats(int|string|null $pharmacyId): void
    {
        if ($pharmacyId !== null) {
            Cache::forget(self::cacheKey($pharmacyId));
        }
    }
}
