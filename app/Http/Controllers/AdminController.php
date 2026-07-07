<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductBatch;

class AdminController extends Controller
{
    //
    public function index(){
        $lowStockThreshold = config('inventory.low_stock_threshold', 10);
        $expiringDays = config('inventory.expiring_days', 30);

        $totalProducts = Product::count();
        $totalStock = ProductBatch::sum('quantity');
        $expiringSoon = ProductBatch::whereDate('expiry_date', '<=', now()->addDays($expiringDays))
            ->where('quantity', '>', 0)
            ->count();

        $lowStockProducts = Product::withSum('batches as total_stock', 'quantity')
            ->having('total_stock', '<=', $lowStockThreshold)
            ->count();

        return view('admin.dashboard', [
            'totalProducts' => $totalProducts,
            'totalStock' => $totalStock,
            'expiringSoon' => $expiringSoon,
            'lowStockProducts' => $lowStockProducts,
            'lowStockThreshold' => $lowStockThreshold,
            'expiringDays' => $expiringDays,
        ]);
    }
}
